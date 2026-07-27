<?php

namespace App\Console\Commands;

use App\Models\AdmissionStudent;
use App\Models\SchoolFeeTemplate;
use App\Models\SchoolStudentFee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FeesProcessFines extends Command
{
    protected $signature = 'fees:process-fines';
    protected $description = 'Auto-add fine fee records for students with overdue fees';

    public function handle(): void
    {
        $now = now();
        $created = 0;

        $overdueFees = SchoolStudentFee::whereIn('status', ['over_due', 'over_due_partial'])
            ->get(['school_id', 'student_id', 'fee_type_name', 'fee_template_id']);

        $processed = [];

        foreach ($overdueFees as $fee) {
            $student = AdmissionStudent::find($fee->student_id);
            if (!$student) continue;

            $classId = $this->resolveId('school_classes', 'class_name', $student->class);
            $sessionId = $this->resolveId('school_sessions', 'session_year', $student->session);
            if (!$classId || !$sessionId) continue;

            $fineTemplates = SchoolFeeTemplate::where('school_id', $fee->school_id)
                ->where('fee_type_name', 'Fine')
                ->where('is_active', true)
                ->where('class_id', $classId)
                ->where('session_id', $sessionId)
                ->get(['id', 'fee_name', 'amount']);

            foreach ($fineTemplates as $template) {
                $key = $fee->student_id . '_' . $template->id . '_' . $now->format('Y-m');
                if (isset($processed[$key])) continue;

                $exists = SchoolStudentFee::where('student_id', $fee->student_id)
                    ->where('fee_template_id', $template->id)
                    ->where('pay_date', 'like', $now->format('Y-m') . '-%')
                    ->exists();

                if ($exists) continue;

                SchoolStudentFee::create([
                    'school_id'       => $fee->school_id,
                    'student_id'      => $fee->student_id,
                    'fee_template_id' => $template->id,
                    'fee_type_name'   => 'Fine',
                    'fee_name'        => $template->fee_name,
                    'base_amount'     => $template->amount,
                    'pay_date'        => $now->toDateString(),
                    'status'          => 'over_due',
                ]);

                $processed[$key] = true;
                $created++;
            }
        }

        $this->info("Created {$created} fine fee record(s).");
    }

    private function resolveId(string $table, string $nameColumn, string|int $value): ?int
    {
        if (is_numeric($value)) {
            $row = DB::table($table)->find((int)$value);
            return $row ? (int)$row->id : null;
        }

        $row = DB::table($table)->where($nameColumn, $value)->first();
        return $row ? (int)$row->id : null;
    }
}
