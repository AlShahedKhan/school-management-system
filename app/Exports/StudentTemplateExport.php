<?php

namespace App\Exports;

use App\Enums\SmsType;

use Maatwebsite\Excel\Concerns\FromArray;

use Maatwebsite\Excel\Concerns\WithColumnFormatting;

use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use Maatwebsite\Excel\Concerns\WithTitle;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StudentTemplateExport implements WithMultipleSheets
{
    public function __construct(
        private string  $className,

        private ?string $groupName,

        private string  $sectionName,

        private string  $sessionYear,
    ) {}

    public function sheets(): array
    {
        return [
            new StudentTemplateDataSheet(
                $this->className,

                $this->groupName,

                $this->sectionName,

                $this->sessionYear,
            ),

            new StudentTemplateInfoSheet(
                $this->className,

                $this->groupName,

                $this->sectionName,

                $this->sessionYear,
            ),
        ];
    }
}

class StudentTemplateDataSheet implements FromArray, WithHeadings, WithTitle, WithColumnFormatting
{
    public function __construct(
        private string  $className,

        private ?string $groupName,

        private string  $sectionName,

        private string  $sessionYear,
    ) {}

    public function title(): string
    {
        return 'Student Data';
    }

    public function headings(): array
    {
        return [
            'student_name',

            'father_name',

            'mother_name',

            'mobile',

            'current_country',

            'current_division',

            'current_district',

            'current_upazila',

            'current_village',

            'permanent_country',

            'permanent_division',

            'permanent_district',

            'permanent_upazila',

            'permanent_village',

            'guardian_name',

            'guardian_relation',

            'guardian_mobile',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Rahim Ahmed',

                'Karim Ahmed',

                'Fatema Begum',

                '01712345678',

                'Bangladesh',

                'Dhaka',

                'Gazipur',

                'Gazipur Sadar',

                'Rowshon Market',

                'Bangladesh',

                'Dhaka',

                'Gazipur',

                'Gazipur Sadar',

                'Rowshon Market',

                'Mohammad Ali',

                'Father',

                '01712345679',
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT,

            'Q' => NumberFormat::FORMAT_TEXT,
        ];
    }
}

class StudentTemplateInfoSheet implements FromArray, WithHeadings, WithTitle
{
    public function __construct(
        private string  $className,

        private ?string $groupName,

        private string  $sectionName,

        private string  $sessionYear,
    ) {}

    public function title(): string
    {
        return 'Template Info (Do Not Edit)';
    }

    public function headings(): array
    {
        return ['Field', 'Value'];
    }

    public function array(): array
    {
        return [
            ['Class',   $this->className],

            ['Group',   $this->groupName ?? '—'],

            ['Section', $this->sectionName],

            ['Session', $this->sessionYear],
        ];
    }
}
