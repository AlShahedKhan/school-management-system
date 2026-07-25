<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ExamNameExport implements FromCollection, WithHeadings, WithTitle
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
                'class_name' => $record->class_name,
                'group_name' => $record->group_name,
                'section_name' => $record->section_name,
                'session_name' => $record->session_name,
                'exam_name' => $record->exam_name,
            ];
        });
    }

    public function headings(): array
    {
        return ['Class', 'Group', 'Section', 'Session', 'Exam Name'];
    }

    public function title(): string
    {
        return 'Exam Names';
    }
}
