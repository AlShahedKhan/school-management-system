<?php

namespace App\Services;

use App\Models\SchoolFeeTemplate;
use App\Models\SchoolStudentFee;
use App\Models\AdmissionStudent;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class SchoolStudentFeeGenerationService
{
    protected $discountService;

    public function __construct(SchoolFeeDiscountService $discountService)
    {
        $this->discountService = $discountService;
    }


    public function generateAdmissionFee(AdmissionStudent $student, $classId, $sessionId, $schoolId = null)
    {
        $query = SchoolFeeTemplate::where(function ($q) {
            $q->whereHas('assign', function ($q) {
                $q->where('name', 'Admission');
            })->orWhere('fee_type_name', 'Admission');
        })
        ->where('class_id', $classId)
        ->where('session_id', $sessionId)
        ->where('is_active', true);

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $template = $query->first();

        if (!$template) {
            throw new Exception("Active Admission Fee Template not found. Cannot generate fee.");
        }


        $exists = SchoolStudentFee::where('student_id', $student->id)
            ->where('fee_template_id', $template->id)
            ->exists();

        if ($exists) {
            return false;
        }


        $dueDate = $template->pay_date
            ? Carbon::parse($template->pay_date)
            : Carbon::now()->addDays(7);

        return $this->generateFeeRecord($template, $student, $dueDate);
    }


    public function generatePromoteFee(AdmissionStudent $student, $classId, $sessionId, $schoolId = null)
    {
        $query = SchoolFeeTemplate::where(function ($q) {
            $q->where('fee_type_name', 'like', '%Promote%')
              ->orWhere('fee_name', 'like', '%Promote%');
        })
        ->where('class_id', $classId)
        ->where('session_id', $sessionId)
        ->where('is_active', true);

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $template = $query->first();

        if (!$template) {
            throw new Exception("Active Promote Fee Template not found. Cannot generate fee.");
        }


        $exists = SchoolStudentFee::where('student_id', $student->id)
            ->where('fee_template_id', $template->id)
            ->exists();

        if ($exists) {
            return false;
        }


        $dueDate = $template->pay_date
            ? Carbon::parse($template->pay_date)
            : Carbon::now()->addDays(7);

        return $this->generateFeeRecord($template, $student, $dueDate);
    }


    public function generateMonthlyFees(SchoolFeeTemplate $template, $monthYear)
    {
        if ($template->assign->payment_type !== 'monthly') {
            throw new Exception("Template is not a monthly fee.");
        }


        $students = $this->getActiveStudentsForTemplate($template);

        DB::transaction(function () use ($template, $students, $monthYear) {
            foreach ($students as $student) {





                $exists = SchoolStudentFee::where('student_id', $student->id)
                    ->where('fee_template_id', $template->id)
                    ->whereYear('pay_date', substr($monthYear, 0, 4))
                    ->whereMonth('pay_date', substr($monthYear, 5, 2))
                    ->exists();

                if (!$exists) {
                    $dueDate = Carbon::createFromFormat('Y-m', $monthYear)->day($template->due_day ?? 10);
                    $this->generateFeeRecord($template, $student, $dueDate, $monthYear);
                }
            }
        });
    }


    public function generateOneTimeFee(SchoolFeeTemplate $template, array $studentIds = [])
    {
        if ($template->assign->payment_type !== 'one_time') {
            throw new Exception("Template is not a one-time fee.");
        }

        $students = count($studentIds) > 0
            ? AdmissionStudent::whereIn('id', $studentIds)->where('status', 'active')->get()
            : $this->getActiveStudentsForTemplate($template);

        DB::transaction(function () use ($template, $students) {
            foreach ($students as $student) {
                $exists = SchoolStudentFee::where('student_id', $student->id)
                    ->where('fee_template_id', $template->id)
                    ->exists();

                if (!$exists) {
                    $dueDate = $template->pay_date ? Carbon::parse($template->pay_date) : Carbon::now()->addDays(7);
                    $this->generateFeeRecord($template, $student, $dueDate);
                }
            }
        });
    }


    protected function generateFeeRecord(SchoolFeeTemplate $template, AdmissionStudent $student, Carbon $dueDate, $generationPeriod = 'one_time')
    {
        $amounts = $this->discountService->calculatePayableAmount($template, $student);


        $exists = SchoolStudentFee::where('student_id', $student->id)
            ->where('fee_template_id', $template->id)
            ->where('generation_period', $generationPeriod)
            ->exists();

        if ($exists) {
            return false;
        }

        return SchoolStudentFee::create([
            'school_id' => $template->school_id,
            'student_id' => $student->id,
            'fee_assign_id' => $template->fee_assign_id,
            'fee_template_id' => $template->id,
            'fee_type_name' => $template->fee_type_name ?? $template->assign->name,
            'fee_name' => $template->fee_name,
            'base_amount' => $template->amount,
            'discount_amount' => $amounts['discount_amount'],
            'payable_amount' => $amounts['payable_amount'],
            'paid_amount' => 0,
            'due_amount' => $amounts['payable_amount'],
            'advance_amount' => 0,
            'generation_period' => $generationPeriod,
            'pay_date' => Carbon::now()->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'status' => $amounts['payable_amount'] <= 0 ? 'paid' : 'unpaid',
        ]);
    }


    protected function getActiveStudentsForTemplate(SchoolFeeTemplate $template)
    {
        $query = AdmissionStudent::where('status', 'active')
            ->where('school_id', $template->school_id)
            ->where('class_id', $template->class_id)
            ->where('session_id', $template->session_id);

        if ($template->group_id) {
            $query->where('group_id', $template->group_id);
        }

        if ($template->section_id) {
            $query->where('section_id', $template->section_id);
        }

        return $query->get();
    }
}
