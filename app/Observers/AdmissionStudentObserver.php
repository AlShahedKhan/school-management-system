<?php

namespace App\Observers;

use App\Models\AdmissionStudent;
use App\Services\SchoolExamAdmitCardAutomationService;

class AdmissionStudentObserver
{
    public function created(AdmissionStudent $student): void
    {
        app(SchoolExamAdmitCardAutomationService::class)->generateForStudent($student);
    }

    public function updated(AdmissionStudent $student): void
    {
        if (! $student->wasChanged(['class_id', 'group_id', 'section_id', 'session_id', 'status'])) {
            return;
        }

        app(SchoolExamAdmitCardAutomationService::class)->generateForStudent($student);
    }
}
