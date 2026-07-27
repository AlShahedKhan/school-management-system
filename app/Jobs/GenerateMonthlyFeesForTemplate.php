<?php

namespace App\Jobs;

use App\Models\AdmissionStudent;
use App\Models\AdvancePayment;
use App\Models\SchoolFeeTemplate;
use App\Models\SchoolPayment;
use App\Models\SchoolStudentFee;
use App\Models\StudentPromotion;
use App\Models\StudentReadmission;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyFeesForTemplate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $backoff = [30, 60, 120, 300, 600];

    protected int $templateId;
    protected int $offset;
    protected int $chunkSize = 500;

    public function __construct(int $templateId, int $offset = 0)
    {
        $this->templateId = $templateId;
        $this->offset = $offset;
    }

    public function handle(): void
    {
        $template = SchoolFeeTemplate::find($this->templateId);
        if (!$template || !$template->is_active) {
            return;
        }

        $school = DB::table('schools')->find($template->school_id);
        if (!$school) return;
        $schoolOwnerUserId = $school->user_id;

        $class = DB::table('school_classes')->find($template->class_id);
        $schoolSession = DB::table('school_sessions')->find($template->session_id);
        if (!$class || !$schoolSession) return;

        $now = Carbon::now();
        $yearMonth = $now->format('Y-m');
        $currentMonthStart = $now->copy()->startOfMonth();

        // Session date boundaries
        $sessionStartDate = $schoolSession->start_date ? Carbon::parse($schoolSession->start_date)->startOfMonth() : null;
        $sessionEndDate = $schoolSession->end_date ? Carbon::parse($schoolSession->end_date)->startOfMonth() : null;

        // Skip if session hasn't started yet or has already ended
        if ($sessionStartDate && $currentMonthStart->lt($sessionStartDate)) {
            return;
        }
        if ($sessionEndDate && $currentMonthStart->gt($sessionEndDate)) {
            return;
        }

        if ($template->frequency === 'one_time' || $template->frequency === 'per_exam') {
            $payDate = $template->pay_date;
        } else {
            $dueDay = $template->due_day ?? $now->day;
            $payDate = "{$yearMonth}-" . str_pad(min($dueDay, 28), 2, '0', STR_PAD_LEFT);
        }

        if ($template->fee_type_name === 'Food' && !empty($template->student_ids)) {
            $studentIds = $template->student_ids;
            $total = AdmissionStudent::whereIn('id', $studentIds)->count();
            if ($this->offset >= $total) return;
            $students = AdmissionStudent::whereIn('id', $studentIds)
                ->skip($this->offset)->take($this->chunkSize)->get(['id', 'admission_date']);
        } else {
            $studentQuery = AdmissionStudent::where('school_id', $schoolOwnerUserId)
                ->where(function ($q) use ($template, $class) {
                    $q->where('class', $template->class_id)
                        ->orWhere('class', $class->class_name);
                })
                ->where(function ($q) use ($template, $schoolSession) {
                    $q->where('session', $template->session_id)
                        ->orWhere('session', $schoolSession->session_year);
                });

            if ($template->group_id) {
                $group = DB::table('school_groups')->find($template->group_id);
                if ($group) {
                    $studentQuery->where(function ($q) use ($template, $group) {
                        $q->where('group', $template->group_id)
                            ->orWhere('group', $group->group_name);
                    });
                }
            }
            if ($template->section_id) {
                $section = DB::table('school_sections')->find($template->section_id);
                if ($section) {
                    $studentQuery->where(function ($q) use ($template, $section) {
                        $q->where('section', $template->section_id)
                            ->orWhere('section', $section->section_name);
                    });
                }
            }

            $total = $studentQuery->count();
            if ($this->offset >= $total) return;

            $students = $studentQuery->skip($this->offset)->take($this->chunkSize)->get(['id', 'admission_date']);
        }
        if ($students->isEmpty()) {
            return;
        }

        $studentIds = $students->pluck('id')->toArray();

        $existing = SchoolStudentFee::where('fee_template_id', $template->id)
            ->whereIn('student_id', $studentIds)
            ->where('pay_date', 'like', "{$yearMonth}-%")
            ->pluck('student_id')
            ->toArray();

        $newStudentIds = array_diff($studentIds, $existing);

        // Filter students by fee start date (Admission Date, Promotion Date, or Re-Admission Date)
        if (!empty($newStudentIds)) {
            $studentDateMap = [];
            foreach ($students as $s) {
                $studentDateMap[$s->id] = $s->admission_date ? Carbon::parse($s->admission_date) : null;
            }

            // Load latest promotion dates for these students in this class+session
            $promotions = StudentPromotion::whereIn('student_id', $newStudentIds)
                ->where('to_class_id', $template->class_id)
                ->where('to_session_id', $template->session_id)
                ->select('student_id', 'promote_date')
                ->get()
                ->groupBy('student_id')
                ->map(fn ($rows) => $rows->sortByDesc('id')->first()->promote_date);

            // Load latest readmission dates for these students in this session
            $readmissions = StudentReadmission::whereIn('student_id', $newStudentIds)
                ->where('to_session_id', $template->session_id)
                ->select('student_id', 'readmission_date')
                ->get()
                ->groupBy('student_id')
                ->map(fn ($rows) => $rows->sortByDesc('id')->first()->readmission_date);

            $filtered = [];
            foreach ($newStudentIds as $sid) {
                $startDate = $studentDateMap[$sid] ?? null;
                $promoteDate = isset($promotions[$sid]) ? Carbon::parse($promotions[$sid]) : null;
                $readmitDate = isset($readmissions[$sid]) ? Carbon::parse($readmissions[$sid]) : null;

                // Fee start date is the latest of admission, promotion, or readmission
                $feeStartDate = $startDate;
                if ($promoteDate && (!$feeStartDate || $promoteDate->gt($feeStartDate))) {
                    $feeStartDate = $promoteDate;
                }
                if ($readmitDate && (!$feeStartDate || $readmitDate->gt($feeStartDate))) {
                    $feeStartDate = $readmitDate;
                }

                // Skip if student's fee start month is after the current month
                if ($feeStartDate && $feeStartDate->copy()->startOfMonth()->gt($currentMonthStart)) {
                    continue;
                }

                $filtered[] = $sid;
            }
            $newStudentIds = $filtered;
        }

        if (!empty($newStudentIds)) {
            // Load advance credits for these students
            $credits = AdvancePayment::whereIn('student_id', $newStudentIds)
                ->where('remaining_credit', '>', 0)
                ->select('student_id', 'remaining_credit')
                ->get()
                ->groupBy('student_id')
                ->map(function ($group) {
                    return $group->sum('remaining_credit');
                });

            $rows = [];
            $creditDeductions = [];
            $nowTimestamp = now();
            foreach ($newStudentIds as $sid) {
                $amount = (float) $template->amount;

                // Check orphan payments (whereNull school_student_fee_id) for this month
                $orphanPayments = SchoolPayment::where('school_id', $template->school_id)
                    ->whereNull('school_student_fee_id')
                    ->where('admission_student_id', $sid)
                    ->where('fees_type', $template->fee_type_name)
                    ->where('fee_name', $template->fee_name)
                    ->where('for_month', $yearMonth)
                    ->get();

                $advancePaid = 0;
                foreach ($orphanPayments as $op) {
                    $advancePaid += (float) $op->type_amount;
                }

                $status = 'pending';

                if ($advancePaid >= $amount) {
                    $status = 'advance';
                } elseif ($advancePaid > 0) {
                    $status = 'advance_partial';
                } else {
                    // Fallback: old AdvancePayment table
                    $credit = isset($credits[$sid]) ? (float) $credits[$sid] : 0;
                    if ($credit >= $amount) {
                        $status = 'advance';
                        $creditDeductions[$sid] = ($creditDeductions[$sid] ?? 0) + $amount;
                    } elseif ($credit > 0) {
                        $status = 'advance_partial';
                        $creditDeductions[$sid] = ($creditDeductions[$sid] ?? 0) + $credit;
                    }
                }

                // If status is still pending and the due day has passed, mark as due/over_due
                if ($status === 'pending' && $payDate) {
                    $dueDate = Carbon::parse($payDate);
                    if ($dueDate->lte($now)) {
                        $status = $dueDate->copy()->startOfMonth()->lt($now->copy()->startOfMonth())
                            ? 'over_due'
                            : 'due';
                    }
                }

                $rows[] = [
                    'school_id'       => $template->school_id,
                    'student_id'      => $sid,
                    'fee_template_id' => $template->id,
                    'fee_type_name'   => $template->fee_type_name,
                    'fee_name'        => $template->fee_name,
                    'base_amount'     => $template->amount,
                    'pay_date'        => $payDate,
                    'status'          => $status,
                    'created_at'      => $nowTimestamp,
                    'updated_at'      => $nowTimestamp,
                ];
            }

            SchoolStudentFee::insert($rows);

            $insertedFees = SchoolStudentFee::where('fee_template_id', $template->id)
                ->whereIn('student_id', $newStudentIds)
                ->where('created_at', $nowTimestamp)
                ->get(['id', 'student_id', 'fee_template_id']);

            // Link orphan payments to the newly created fee records
            foreach ($insertedFees as $fee) {
                SchoolPayment::where('school_id', $template->school_id)
                    ->whereNull('school_student_fee_id')
                    ->where('admission_student_id', $fee->student_id)
                    ->where('fees_type', $template->fee_type_name)
                    ->where('fee_name', $template->fee_name)
                    ->where('for_month', $yearMonth)
                    ->update(['school_student_fee_id' => $fee->id]);
            }

            // Apply credit deductions
            foreach ($creditDeductions as $sid => $deductAmount) {
                AdvancePayment::where('student_id', $sid)
                    ->where('remaining_credit', '>', 0)
                    ->decrement('remaining_credit', $deductAmount);
            }
        }

        $fetchedCount = count($students);
        if ($fetchedCount >= $this->chunkSize) {
            self::dispatch($this->templateId, $this->offset + $this->chunkSize)
                ->delay(now()->addSeconds(2));
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateMonthlyFeesForTemplate failed: template_id={$this->templateId}, offset={$this->offset}", [
            'error' => $exception->getMessage(),
        ]);
    }
}
