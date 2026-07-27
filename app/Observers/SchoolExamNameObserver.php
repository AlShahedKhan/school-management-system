<?php

namespace App\Observers;

use App\Models\SchoolExamName;
use App\Models\SchoolFeeTemplate;
use App\Models\SchoolStudentFee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolExamNameObserver
{
    public function updated(SchoolExamName $exam): void
    {
        if (!$exam->wasChanged('exam_end_date')) {
            return;
        }

        $oldEndDate = $exam->getOriginal('exam_end_date');
        $newEndDate = $exam->exam_end_date;

        if ($oldEndDate === $newEndDate) {
            return;
        }

        try {
            DB::transaction(function () use ($exam, $newEndDate) {
                $templateIds = SchoolFeeTemplate::where('exam_id', $exam->id)
                    ->pluck('id');

                if ($templateIds->isEmpty()) {
                    return;
                }

                SchoolFeeTemplate::whereIn('id', $templateIds)
                    ->update(['pay_date' => $newEndDate]);

                SchoolStudentFee::whereIn('fee_template_id', $templateIds)
                    ->whereNotIn('status', ['paid'])
                    ->update(['due_date' => $newEndDate]);
            });
        } catch (\Exception $e) {
            Log::error("Failed to sync exam fee due dates after exam update: {$e->getMessage()}");
        }
    }
}
