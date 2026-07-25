<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExamGradeExport implements FromCollection, WithHeadings
{
    protected $grades;

    public function __construct($grades)
    {
        $this->grades = $grades;
    }

    public function collection()
    {
        return $this->grades->map(function ($grade, $index) {
            return [
                'SL' => $index + 1,
                'Minimum mark' => $grade->mark_from,
                'Maximum mark' => $grade->mark_to,
                'Letter name' => $grade->grade_name,
                'Point no' => $grade->grade_point,
                'Total Mark' => $grade->full_mark,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'SL',
            'Minimum mark',
            'Maximum mark',
            'Letter name',
            'Point no',
            'Total Mark',
        ];
    }
}
