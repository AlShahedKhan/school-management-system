<?php

namespace App\Console\Commands;

use App\Models\SchoolDiscount;
use App\Models\DiscountStudent;
use App\Models\DiscountExamLog;
use App\Models\SchoolExamMark;
use App\Models\SchoolExamName;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DiscountsCheckExamWaivers extends Command
{
    protected $signature = 'discounts:check-exam-waivers';
    protected $description = 'Check exam results for exam_waiver discounts and auto-cancel/resume';

    public function handle(): void
    {
        $waiverDiscounts = SchoolDiscount::with(['discountStudents.student'])
            ->where('discount_category', 'exam_waiver')
            ->where('is_active', true)
            ->get();

        if ($waiverDiscounts->isEmpty()) {
            $this->info('No active exam waiver discounts found.');
            return;
        }

        $processed = 0;

        foreach ($waiverDiscounts as $discount) {
            foreach ($discount->discountStudents as $ds) {
                if (!$ds->student) continue;

                $studentIdNumber = $ds->student->student_id_number;

                // Find the latest exam this student appeared in (across ALL exams)
                $latestExam = SchoolExamMark::where('student_id_number', $studentIdNumber)
                    ->select('exam_name', 'session_name')
                    ->orderBy('created_at', 'desc')
                    ->first();

                if (!$latestExam) continue;

                // Get ALL subject marks for that exam to calculate overall GPA
                $marks = SchoolExamMark::where('student_id_number', $studentIdNumber)
                    ->where('exam_name', $latestExam->exam_name)
                    ->where('session_name', $latestExam->session_name)
                    ->get();

                if ($marks->isEmpty()) continue;

                // Overall GPA = average of all subject points
                $overallGpa = $marks->avg('point');
                $overallMarks = $marks->avg('mark');

                $passed = true;
                if ($discount->min_gpa && $overallGpa < (float) $discount->min_gpa) {
                    $passed = false;
                }
                if ($discount->min_marks && $overallMarks < (float) $discount->min_marks) {
                    $passed = false;
                }

                $oldStatus = $ds->status;

                if ($passed) {
                    if ($ds->status === 'cancelled') {
                        $ds->update([
                            'status' => 'active',
                            'cancel_reason' => null,
                            'cancelled_at' => null,
                        ]);
                        $action = 'resumed';
                    } else {
                        $action = 'granted';
                    }
                } else {
                    if ($ds->status === 'active') {
                        $ds->update([
                            'status' => 'cancelled',
                            'cancel_reason' => 'exam_failed',
                            'cancelled_at' => Carbon::now(),
                        ]);
                        $action = 'cancelled';
                    } else {
                        $action = 'cancelled';
                    }
                }

                // Look up exam_id from school_exam_names for audit log
                $session = \App\Models\SchoolSession::where('session_year', $latestExam->session_name)->first();
                $examNameRecord = SchoolExamName::where('exam_name', $latestExam->exam_name)
                    ->where('session_id', $session?->id)
                    ->first();

                if ($examNameRecord) {
                    DiscountExamLog::create([
                        'discount_student_id' => $ds->id,
                        'exam_id'             => $examNameRecord->id,
                        'exam_gpa'           => round($overallGpa, 2),
                        'exam_marks'         => round($overallMarks, 2),
                        'passed'             => $passed,
                        'action_taken'       => $action,
                    ]);
                }

                $processed++;
            }
        }

        $this->info("Processed {$processed} exam waiver checks.");
    }
}
