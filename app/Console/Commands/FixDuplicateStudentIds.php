<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AdmissionStudent;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\DB;

class FixDuplicateStudentIds extends Command
{
    protected $signature = 'students:fix-duplicate-ids';

    protected $description = 'Finds and fixes duplicate student_id_number values by assigning unique sequential IDs.';

    public function handle()
    {
        $this->info('Searching for duplicate student_id_number entries...');

        $duplicateGroups = DB::table('admission_students')
            ->select('school_id', 'student_id_number', DB::raw('COUNT(*) as count'))
            ->whereNotNull('student_id_number')
            ->where('student_id_number', '!=', '')
            ->groupBy('school_id', 'student_id_number')
            ->having('count', '>', 1)
            ->get();

        if ($duplicateGroups->isEmpty()) {
            $this->info('No duplicate student_id_number entries found.');
            return 0;
        }

        $this->warn("Found {$duplicateGroups->count()} groups of duplicate student IDs.");
        $totalFixed = 0;

        foreach ($duplicateGroups as $group) {
            $schoolId = $group->school_id;
            $school = School::find($schoolId) ?? School::where('user_id', $schoolId)->first();
            if (!$school) {
                continue;
            }

            $schoolUser = User::find($school->user_id);
            $schoolPrefix = $schoolUser?->id_number ? substr($schoolUser->id_number, -5) : '00000';

            $students = AdmissionStudent::where('school_id', $schoolId)
                ->where('student_id_number', $group->student_id_number)
                ->orderBy('id', 'asc')
                ->get();

            // Skip the first record (keep original), update all remaining duplicate records
            $duplicatesToFix = $students->slice(1);
            $nextSerial = generate_school_common_next_serial($schoolId);

            foreach ($duplicatesToFix as $student) {
                $oldId = $student->student_id_number;
                $newId = $schoolPrefix . str_pad($nextSerial++, 6, '0', STR_PAD_LEFT);

                DB::transaction(function () use ($student, $oldId, $newId, $schoolUser) {
                    $student->update(['student_id_number' => $newId]);

                    // Update corresponding user record
                    User::where('role', 'student')
                        ->where('school_name', $schoolUser?->school_name)
                        ->where('name', $student->student_name)
                        ->where('mobile', $student->mobile)
                        ->update(['id_number' => $newId]);
                });

                $this->line("Fixed Student ID [{$student->student_name}]: {$oldId} -> {$newId}");
                $totalFixed++;
            }
        }

        $this->info("Successfully re-assigned {$totalFixed} duplicate student IDs with unique numbers.");
        return 0;
    }
}
