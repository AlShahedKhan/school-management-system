<?php

namespace App\Console\Commands;

use App\Models\AdmissionStudent;
use App\Services\SchoolExamAdmitCardAutomationService;
use Illuminate\Console\Command;

class GenerateImminentExamAdmitCards extends Command
{
    protected $signature = 'school:generate-active-exam-admit-cards';

    protected $description = 'Generate admit cards for active students before matching exams end';

    public function handle(SchoolExamAdmitCardAutomationService $service): int
    {
        $created = 0;

        AdmissionStudent::query()
            ->whereIn('status', ['Active', 'active', 'approved'])
            ->whereNotNull('class_id')
            ->whereNotNull('session_id')
            ->whereNotNull('student_id_number')
            ->chunkById(500, function ($students) use ($service, &$created) {
                foreach ($students as $student) {
                    $created += $service->generateForStudent($student);
                }
            });

        $this->info("Created {$created} automatic admit card(s).");

        return self::SUCCESS;
    }
}
