<?php

namespace App\Http\Controllers\Api;

use App\Exports\StudentTemplateExport;

use App\Http\Controllers\Controller;

use App\Imports\StudentBulkImport;

use App\Models\AdmissionStudent;

use App\Models\Guardian;

use App\Models\School;

use App\Models\SchoolClass;

use App\Models\SchoolFeeTemplate;

use App\Models\SchoolGroup;

use App\Models\SchoolSection;

use App\Models\SchoolSession;

use App\Models\User;

use App\Services\SchoolStudentFeeGenerationService;

use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Validator;

use Maatwebsite\Excel\Facades\Excel;

class SchoolBulkUploadController extends Controller
{
    private function authorizeSelection(int $schoolInternalId, int $classId, ?int $groupId, int $sectionId, int $sessionId): ?string
    {
        if (!SchoolClass::where('id', $classId)->where('school_id', $schoolInternalId)->exists()) {
            return 'The selected class does not belong to your school.';
        }

        if (!SchoolSection::where('id', $sectionId)->where('school_id', $schoolInternalId)->exists()) {
            return 'The selected section does not belong to your school.';
        }

        if (!SchoolSession::where('id', $sessionId)->where('school_id', $schoolInternalId)->exists()) {
            return 'The selected session does not belong to your school.';
        }

        $groupValid = match ($groupId) {
            null => true,

            default => SchoolGroup::where('id', $groupId)
                ->where('school_id', $schoolInternalId)
                ->where('class_id', $classId)
                ->exists()
        };

        if (!$groupValid) {
            return 'The selected group does not belong to this class in your school.';
        }

        return null;
    }

    public function downloadTemplate(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $request->validate([
            'class_id'   => 'required|exists:school_classes,id',

            'group_id'   => 'nullable|exists:school_groups,id',

            'section_id' => 'required|exists:school_sections,id',

            'session_id' => 'required|exists:school_sessions,id',
        ]);

        $schoolInternalId = School::where('user_id', Auth::id())->value('id');

        if (!$schoolInternalId) {
            abort(403, 'School profile not found.');
        }

        $classId   = (int) $request->class_id;

        $groupId   = $request->group_id ? (int) $request->group_id : null;

        $sectionId = (int) $request->section_id;

        $sessionId = (int) $request->session_id;

        $authError = $this->authorizeSelection($schoolInternalId, $classId, $groupId, $sectionId, $sessionId);

        if ($authError) {
            abort(403, $authError);
        }

        $class   = SchoolClass::find($classId);

        $section = SchoolSection::find($sectionId);

        $session = SchoolSession::find($sessionId);

        $group   = $groupId ? SchoolGroup::find($groupId) : null;

        $slug = fn(string $s) => strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($s)));

        $filename = 'students-' . $slug($class->class_name);

        $filename = match ($group ? true : false) {
            true => $filename . '-' . $slug($group->group_name),

            false => $filename
        };

        $filename .= '-' . $slug($section->section_name) . '-' . $slug($session->session_year) . '.xlsx';

        return Excel::download(
            new StudentTemplateExport(
                $class->class_name,

                $group?->group_name,

                $section->section_name,

                $session->session_year,
            ),

            $filename
        );
    }

    public function upload(Request $request): JsonResponse
    {
        set_time_limit(0);

        $request->validate([
            'file'           => 'required|file|mimes:xlsx,xls|max:10240',

            'class_id'       => 'required|exists:school_classes,id',

            'group_id'       => 'nullable|exists:school_groups,id',

            'section_id'     => 'required|exists:school_sections,id',

            'session_id'     => 'required|exists:school_sessions,id',

            'admission_date' => 'required|date',
        ]);

        $schoolUser = Auth::user();

        $schoolId   = $schoolUser->id;

        $schoolName = $schoolUser->school_name;

        $schoolInternalId = School::where('user_id', $schoolId)->value('id');

        if (!$schoolInternalId) {
            return response()->json(['message' => 'School profile not found.'], 403);
        }

        $classId   = (int) $request->class_id;

        $groupId   = $request->group_id ? (int) $request->group_id : null;

        $sectionId = (int) $request->section_id;

        $sessionId = (int) $request->session_id;

        $authError = $this->authorizeSelection($schoolInternalId, $classId, $groupId, $sectionId, $sessionId);

        if ($authError) {
            return response()->json(['message' => $authError], 403);
        }

        // Enforce: Admission Fee Template must exist
        $templateExists = SchoolFeeTemplate::where('school_id', $schoolInternalId)
            ->where('class_id', $classId)
            ->where('session_id', $sessionId)
            ->where(function ($q) {
                $q->where('fee_type_name', 'Admission')
                  ->orWhereHas('assign', fn ($q) => $q->where('name', 'Admission'));
            })
            ->where('is_active', true)
            ->exists();

        if (!$templateExists) {
            return response()->json([
                'message' => 'Bulk upload is not allowed. No active Admission Fee Template exists for this class and session. Please create one first.'
            ], 422);
        }

        try {
            $allSheets = Excel::toArray(new StudentBulkImport(), $request->file('file'));
        } catch (\Exception $e) {
            Log::error('Bulk upload read error: ' . $e->getMessage());

            return response()->json(['message' => 'Could not read the uploaded file. Make sure it is a valid Excel file.'], 422);
        }

        $sheet = $allSheets[0] ?? [];

        if (count($sheet) < 2) {
            return response()->json(['message' => 'The file is empty or contains no data rows.'], 422);
        }

        $rawHeaders = array_map(fn($h) => mb_strtolower(trim((string) $h)), $sheet[0]);

        $dataRows   = [];

        foreach (array_slice($sheet, 1) as $rawRow) {
            $nonEmpty = array_filter($rawRow, fn($v) => trim((string) $v) !== '');

            if (empty($nonEmpty)) {
                continue;
            }

            $row = [];

            foreach ($rawHeaders as $i => $key) {
                $row[$key] = isset($rawRow[$i]) ? trim((string) $rawRow[$i]) : '';
            }

            $dataRows[] = $row;
        }

        if (empty($dataRows)) {
            return response()->json(['message' => 'No data rows found after the header row.'], 422);
        }

        if (count($dataRows) > 500) {
            return response()->json([
                'message' => 'Maximum 500 students per upload. Split into smaller files and upload separately.',
            ], 422);
        }

        $rules = [
            'admission_date'     => 'required|date',

            'student_name'       => 'required|string|max:255',

            'father_name'        => 'required|string|max:255',

            'mother_name'        => 'required|string|max:255',

            'mobile'             => 'required|string',

            'current_country'    => 'nullable|string',

            'current_division'   => 'required|string',

            'current_district'   => 'required|string',

            'current_upazila'    => 'required|string',

            'current_village'    => 'required|string',

            'permanent_country'  => 'nullable|string',

            'permanent_division' => 'required|string',

            'permanent_district' => 'required|string',

            'permanent_upazila'  => 'required|string',

            'permanent_village'  => 'required|string',

            'guardian_name'      => 'required|string|max:255',

            'guardian_relation'  => 'required|string|max:255',

            'guardian_mobile'    => 'required|string',
        ];

        $admissionDate = $request->admission_date;

        $allErrors     = [];

        foreach ($dataRows as $rowIndex => $row) {
            $row['admission_date'] = $admissionDate;

            $dataRows[$rowIndex]   = $row;

            $rowNum    = $rowIndex + 2;

            $validator = Validator::make($row, $rules);

            foreach ($validator->errors()->toArray() as $field => $messages) {
                foreach ($messages as $message) {
                    $allErrors[] = ['row' => $rowNum, 'field' => $field, 'message' => $message];
                }
            }
        }

        if (!empty($allErrors)) {
            return response()->json([
                'message' => 'Validation failed. No students were imported. Fix the errors and re-upload.',

                'errors'  => $allErrors,
            ], 422);
        }

        $count = 0;

        try {
            DB::transaction(function () use (
                $dataRows, $classId, $groupId, $sectionId, $sessionId,
                $schoolId, $schoolName, $schoolInternalId, &$count
            ) {
                foreach ($dataRows as $row) {
                    $guardian = Guardian::create([
                        'name'     => $row['guardian_name'],

                        'relation' => $row['guardian_relation'],

                        'division' => $row['permanent_division'],

                        'district' => $row['permanent_district'],

                        'upazila'  => $row['permanent_upazila'],

                        'village'  => $row['permanent_village'],

                        'mobile'   => $row['guardian_mobile'],
                    ]);

                    $admission = AdmissionStudent::create([
                        'school_id'          => $schoolId,

                        'school'             => $schoolName,

                        'class_id'           => $classId,

                        'section_id'         => $sectionId,

                        'session_id'         => $sessionId,

                        'group_id'           => $groupId,

                        'admission_fee'      => 'N/A',

                        'admission_date'     => $row['admission_date'],

                        'previous_school'    => 'n/a',

                        'previous_class'     => 'n/a',

                        'previous_group'     => 'n/a',

                        'previous_section'   => 'n/a',

                        'previous_session'   => 'n/a',

                        'interview_code'     => 'n/a',

                        'last_exam_result'   => 'n/a',

                        'guardian_id'        => $guardian->id,

                        'student_name'       => $row['student_name'],

                        'father_name'        => $row['father_name'],

                        'mother_name'        => $row['mother_name'],

                        'mobile'             => $row['mobile'],

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

                        'current_country'    => !empty($row['current_country']) ? $row['current_country'] : 'Bangladesh',

                        'permanent_country'  => !empty($row['permanent_country']) ? $row['permanent_country'] : 'Bangladesh',

                        'status'             => 'Active',
                    ]);

                    User::updateOrCreate(
                        [
                            'id_number'   => $admission->student_id_number ?? '',
                        ],
                        [
                            'role'        => 'student',

                            'name'        => $row['student_name'],

                            'school_name' => $schoolName,

                            'mobile'      => $row['mobile'],

                            'password'    => Hash::make('00000000'),
                        ]
                    );

                    // Auto-generate Admission Fee
                    app(SchoolStudentFeeGenerationService::class)->generateAdmissionFee(
                        $admission,
                        $classId,
                        $sessionId,
                        $schoolInternalId
                    );

                    $count++;
                }
            });
        } catch (\Exception $e) {
            Log::error('Bulk upload processing error: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while saving students. No records were created.',

                'error'   => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => "{$count} student(s) imported successfully.",

            'count'   => $count,
        ]);
    }
}
