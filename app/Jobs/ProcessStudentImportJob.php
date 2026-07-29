<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentBulkImport;
use App\Models\AdmissionStudent;
use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\SchoolGroup;
use App\Models\SchoolSection;
use App\Models\SchoolSession;
use App\Models\StudentImport;
use App\Models\User;

class ProcessStudentImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1800;
    public int $tries = 1;

    public function __construct(
        private int $importId,
        private int $schoolUserId,
    ) {}

    public function handle(): void
    {
        $import = StudentImport::findOrFail($this->importId);
        $import->update(['status' => 'processing']);

        try {
            // Read all sheets from the uploaded file
            $allSheets = Excel::toArray(new StudentBulkImport(), Storage::disk('local')->path($import->file_path));
            $sheet     = $allSheets[0] ?? [];

            if (count($sheet) < 2) {
                $import->update([
                    'status' => 'validation_failed',
                    'errors' => [['row' => 0, 'field' => 'file', 'message' => 'The file is empty or contains no data rows.']],
                ]);
                return;
            }

            // Parse headers from row 0; build associative rows from rows 1+
            $rawHeaders = array_map(fn($h) => mb_strtolower(trim((string) $h)), $sheet[0]);
            $dataRows   = [];
            foreach (array_slice($sheet, 1) as $rawRow) {
                // Skip completely empty rows
                $nonEmpty = array_filter($rawRow, fn($v) => trim((string) $v) !== '');
                if (empty($nonEmpty)) continue;
                $row = [];
                foreach ($rawHeaders as $i => $key) {
                    $row[$key] = isset($rawRow[$i]) ? trim((string) $rawRow[$i]) : '';
                }
                $dataRows[] = $row;
            }

            if (empty($dataRows)) {
                $import->update([
                    'status' => 'validation_failed',
                    'errors' => [['row' => 0, 'field' => 'file', 'message' => 'No data rows found after the header row.']],
                ]);
                return;
            }

            $import->update(['total_rows' => count($dataRows)]);

            // Resolve school identifiers
            $schoolUser       = User::findOrFail($this->schoolUserId);
            $schoolInternalId = DB::table('schools')->where('user_id', $this->schoolUserId)->value('id');

            if (!$schoolInternalId) {
                $import->update([
                    'status' => 'failed',
                    'errors' => [['row' => 0, 'field' => 'system', 'message' => 'School profile not found for this account.']],
                ]);
                return;
            }

            // Pre-build in-memory lookup maps (case-insensitive, trimmed)
            $classMap   = SchoolClass::where('school_id', $schoolInternalId)->get()
                ->keyBy(fn($r) => mb_strtolower(trim($r->class_name)));
            $sectionMap = SchoolSection::where('school_id', $schoolInternalId)->get()
                ->keyBy(fn($r) => mb_strtolower(trim($r->section_name)));
            $sessionMap = SchoolSession::where('school_id', $schoolInternalId)->get()
                ->keyBy(fn($r) => mb_strtolower(trim($r->session_year)));
            $groupMap   = SchoolGroup::where('school_id', $schoolInternalId)->get()
                ->keyBy(fn($r) => mb_strtolower(trim($r->group_name)));

            // Load all existing mobiles for O(1) uniqueness check
            $existingMobiles = AdmissionStudent::pluck('mobile')->flip()->all();

            // ── VALIDATION PASS ──────────────────────────────────────────────
            $allErrors   = [];
            $seenMobiles = []; // track within-file duplicates

            $rules = [
                'student_name'       => 'required|string|max:255',
                'father_name'        => 'required|string|max:255',
                'mother_name'        => 'required|string|max:255',
                'mobile'             => 'required|string',
                // Removed password requirement on 2026-07-06 for default password implementation
                'class'              => 'required|string',
                'section'            => 'required|string',
                'session'            => 'required|string',
                'group'              => 'nullable|string',
                // 'admission_fee'      => 'required|numeric|min:0',
                'admission_date'     => 'required|date',
                'current_division'   => 'required|string',
                'current_district'   => 'required|string',
                'current_upazila'    => 'required|string',
                'current_village'    => 'required|string',
                'permanent_division' => 'required|string',
                'permanent_district' => 'required|string',
                'permanent_upazila'  => 'required|string',
                'permanent_village'  => 'required|string',
            ];

            foreach ($dataRows as $rowIndex => $row) {
                $rowNum    = $rowIndex + 2; // +1 for 1-index, +1 for header
                $validator = Validator::make($row, $rules);

                foreach ($validator->errors()->toArray() as $field => $messages) {
                    foreach ($messages as $message) {
                        $allErrors[] = ['row' => $rowNum, 'field' => $field, 'message' => $message];
                    }
                }

                // Mobile: DB uniqueness
                if (!empty($row['mobile'])) {
                    if (isset($existingMobiles[$row['mobile']])) {
                        $allErrors[] = [
                            'row'     => $rowNum,
                            'field'   => 'mobile',
                            'message' => "Mobile '{$row['mobile']}' has already been taken.",
                        ];
                    }
                    // Mobile: within-file duplicate
                    if (isset($seenMobiles[$row['mobile']])) {
                        $allErrors[] = [
                            'row'     => $rowNum,
                            'field'   => 'mobile',
                            'message' => "Duplicate mobile '{$row['mobile']}' — also used in row {$seenMobiles[$row['mobile']]}.",
                        ];
                    } else {
                        $seenMobiles[$row['mobile']] = $rowNum;
                    }
                }

                // Class name lookup
                if (!empty($row['class']) && !isset($classMap[mb_strtolower(trim($row['class']))])) {
                    $allErrors[] = [
                        'row'     => $rowNum,
                        'field'   => 'class',
                        'message' => "Class '{$row['class']}' was not found in your school's class list. See the Reference sheet.",
                    ];
                }

                // Section name lookup
                if (!empty($row['section']) && !isset($sectionMap[mb_strtolower(trim($row['section']))])) {
                    $allErrors[] = [
                        'row'     => $rowNum,
                        'field'   => 'section',
                        'message' => "Section '{$row['section']}' was not found in your school's section list. See the Reference sheet.",
                    ];
                }

                // Session year lookup
                if (!empty($row['session']) && !isset($sessionMap[mb_strtolower(trim($row['session']))])) {
                    $allErrors[] = [
                        'row'     => $rowNum,
                        'field'   => 'session',
                        'message' => "Session '{$row['session']}' was not found in your school's session list. See the Reference sheet.",
                    ];
                }

                // Group name lookup (only if provided)
                if (!empty($row['group']) && !isset($groupMap[mb_strtolower(trim($row['group']))])) {
                    $allErrors[] = [
                        'row'     => $rowNum,
                        'field'   => 'group',
                        'message' => "Group '{$row['group']}' was not found in your school's group list. See the Reference sheet.",
                    ];
                }
            }

            if (!empty($allErrors)) {
                $failedRows = count(array_unique(array_column($allErrors, 'row')));
                $import->update([
                    'status'      => 'validation_failed',
                    'failed_rows' => $failedRows,
                    'errors'      => $allErrors,
                ]);
                return;
            }

            // ── PROCESSING PASS (chunks of 500) ─────────────────────────────
            $processed = 0;
            $schoolPrefix = $schoolUser->id_number ? substr($schoolUser->id_number, -5) : '00000';
            $nextSerial = generate_school_common_next_serial($schoolUser->id);

            foreach (array_chunk($dataRows, 500) as $chunk) {
                DB::transaction(function () use ($chunk, $classMap, $sectionMap, $sessionMap, $groupMap, $schoolUser, $schoolPrefix, &$nextSerial) {
                    foreach ($chunk as $row) {
                        $studentIdNumber = $schoolPrefix . str_pad($nextSerial++, 6, '0', STR_PAD_LEFT);

                        $guardian = Guardian::create([
                            'name'     => !empty($row['guardian_name'])     ? $row['guardian_name']     : 'N/A',
                            'relation' => !empty($row['guardian_relation']) ? $row['guardian_relation'] : 'N/A',
                            'division' => !empty($row['guardian_division']) ? $row['guardian_division'] : 'N/A',
                            'district' => !empty($row['guardian_district']) ? $row['guardian_district'] : 'N/A',
                            'upazila'  => !empty($row['guardian_upazila'])  ? $row['guardian_upazila']  : 'N/A',
                            'village'  => !empty($row['guardian_village'])  ? $row['guardian_village']  : 'N/A',
                            'mobile'   => !empty($row['guardian_mobile'])   ? $row['guardian_mobile']   : 'N/A',
                        ]);

                        $groupId = null;
                        if (!empty($row['group'])) {
                            $groupId = $groupMap[mb_strtolower(trim($row['group']))]->id ?? null;
                        }

                        $admission = AdmissionStudent::create([
                            'school_id'          => $schoolUser->id,
                            'school'             => $schoolUser->school_name,
                            'class_id'           => $classMap[mb_strtolower(trim($row['class']))]->id,
                            'section_id'         => $sectionMap[mb_strtolower(trim($row['section']))]->id,
                            'session_id'         => $sessionMap[mb_strtolower(trim($row['session']))]->id,
                            'group_id'           => $groupId,
                            'admission_fee'      => 'N/A', //$row['admission_fee'],
                            'admission_date'     => $row['admission_date'],
                            'previous_school'    => !empty($row['previous_school'])   ? $row['previous_school']   : 'n/a',
                            'previous_class'     => !empty($row['previous_class'])    ? $row['previous_class']    : 'n/a',
                            'previous_group'     => !empty($row['previous_group'])    ? $row['previous_group']    : 'n/a',
                            'previous_section'   => !empty($row['previous_section'])  ? $row['previous_section']  : 'n/a',
                            'previous_session'   => !empty($row['previous_session'])  ? $row['previous_session']  : 'n/a',
                            'last_exam_result'   => !empty($row['last_exam_result'])  ? $row['last_exam_result']  : 'n/a',
                            'guardian_id'        => $guardian->id,
                            'student_name'       => $row['student_name'],
                            'father_name'        => $row['father_name'],
                            'mother_name'        => $row['mother_name'],
                            'mobile'             => $row['mobile'],
                            // Modified on 2026-07-06: set default password to '00000000'
                            'password'           => Hash::make('00000000'),
                            'image'              => null,
                            'current_division'   => $row['current_division'],
                            'current_district'   => $row['current_district'],
                            'current_upazila'    => $row['current_upazila'],
                            'current_village'    => $row['current_village'],
                            'permanent_division' => $row['permanent_division'],
                            'permanent_district' => $row['permanent_district'],
                            'permanent_upazila'  => $row['permanent_upazila'],
                            'permanent_village'  => $row['permanent_village'],
                            // Modified on 2026-07-07: Use Active status by default for admitted students
                            'status'             => 'Active',
                        ]);

                        User::create([
                            'role'        => 'student',
                            'name'        => $row['student_name'],
                            'school_name' => $schoolUser->school_name,
                            'mobile'      => $row['mobile'],
                            'id_number'   => $studentIdNumber,
                            // Modified on 2026-07-06: set default password to '00000000'
                            'password'    => Hash::make('00000000'),
                        ]);
                    }
                });

                $processed += count($chunk);
                $import->update(['processed_rows' => $processed]);
            }

            $import->update(['status' => 'completed', 'processed_rows' => $processed]);

        } catch (\Exception $e) {
            Log::error("StudentImport #{$this->importId} failed: " . $e->getMessage());
            $import->update([
                'status' => 'failed',
                'errors' => [['row' => 0, 'field' => 'system', 'message' => $e->getMessage()]],
            ]);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error("StudentImport #{$this->importId} job failed unexpectedly: " . $e->getMessage());

        $import = StudentImport::find($this->importId);
        if ($import) {
            $import->update([
                'status' => 'failed',
                'errors' => [['row' => 0, 'field' => 'system', 'message' => 'Job failed: ' . $e->getMessage()]],
            ]);
        }
    }
}
