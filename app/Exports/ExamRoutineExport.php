<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ExamRoutineExport implements FromCollection, WithHeadings, WithTitle
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return collect($this->records)->map(function ($record) {
            return [
                'exam_date' => $record->exam_date,
                'day_name' => $record->day_name,
                'start_time' => $record->start_time,
                'end_time' => $record->end_time,
                'class_name' => $record->schoolClass?->class_name ?? '-',
                'group_name' => $record->schoolGroup?->group_name ?? '-',
                'section_name' => $record->schoolSection?->section_name ?? '-',
                'session_name' => $record->schoolSession?->session_year ?? '-',
                'exam_name' => $record->schoolExam?->exam_name ?? '-',
                'subject_name' => $record->schoolSubject?->subject_name ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['Date', 'Day', 'Start Time', 'End Time', 'Class', 'Group', 'Section', 'Session', 'Exam', 'Subject'];
    }

    public function title(): string
    {
        return 'Exam Routines';
    }
}
