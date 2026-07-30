<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class PaymentSlipExport implements FromArray, WithHeadings, WithEvents, WithStyles
{
    protected $payments;
    protected $student;
    protected $school;
    protected $duration;

    public function __construct($payments, $student, $school, $duration)
    {
        $this->payments = $payments;
        $this->student = $student;
        $this->school = $school;
        $this->duration = $duration;
    }

    public function array(): array
    {
        $rows = [];
        $shownFees = [];

        foreach ($this->payments as $i => $p) {
            $total = (float) ($p->total_payable ?? 0);
            $paid = (float) ($p->type_amount ?? 0);
            $due = max($total - $paid, 0);
            $feeKey = $p->fees_type . '||' . $p->fee_name;
            $showTotal = !in_array($feeKey, $shownFees);
            if ($showTotal) $shownFees[] = $feeKey;

            $receiveMonth = $p->pay_date
                ? Carbon::parse($p->pay_date)->format('F')
                : '-';

            $statusText = $this->getStatus($p->pay_date, $total, $paid);

            $rows[] = [
                $i + 1,
                $p->pay_date ? Carbon::parse($p->pay_date)->format('j-F-Y') : '-',
                $receiveMonth,
                $p->pay_method ?? '-',
                $statusText,
                $p->fees_type ?? '-',
                $p->fee_name ?? '-',
                $showTotal ? number_format($total, 2) : '-',
                number_format($paid, 2),
                number_format($due, 2),
                $due > 0 && $p->pay_date && Carbon::parse($p->pay_date)->startOfDay()->lt(Carbon::today()) ? 'YES' : '-',
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Sl',
            'Pay Date',
            'Receive Month',
            'Receive Method',
            'Receive Status',
            'Fee Type',
            'Fee Name',
            'Payable',
            'Paid',
            'Due',
            'Over Due',
        ];
    }

    public function registerEvents(): array
    {
        $school = $this->school;
        $student = $this->student;
        $duration = $this->duration;

        return [
            AfterSheet::class => function (AfterSheet $event) use ($school, $student, $duration) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'K';

                // Insert 10 rows at the top for header section + gap before table
                $sheet->insertNewRowBefore(1, 10);

                // ── Row 1: School Name ──
                $sheet->setCellValue('A1', $school->school_name ?? 'School Name');
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension('1')->setRowHeight(30);

                // ── Row 2: Address (village + upazila only) ──
                $address = implode(', ', array_filter([$school->village, $school->upazila]));
                $sheet->setCellValue('A2', $address);
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A2')->getFont()->setSize(11);

                // ── Row 3: Mobile ──
                if ($school->mobile) {
                    $sheet->setCellValue('A3', 'Mobile: ' . $school->mobile);
                    $sheet->mergeCells("A3:{$lastCol}3");
                    $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // ── Row 4: blank spacer ──

                // ── Row 5: Student Id | Class ──
                $sheet->setCellValue('A5', 'Student Id: ' . $student->student_id_number);
                $sheet->setCellValue('H5', 'Class: ' . ($student->schoolClass->class_name ?? '—'));
                $sheet->getStyle('A5')->getFont()->setBold(true);
                $sheet->getStyle('H5')->getFont()->setBold(true);

                // ── Row 6: Student Name | Group ──
                $sheet->setCellValue('A6', 'Student Name: ' . $student->student_name);
                $sheet->setCellValue('H6', 'Group: ' . ($student->schoolGroup->group_name ?? '—'));

                // ── Row 7: Duration | Section ──
                $sheet->setCellValue('A7', 'Duration: ' . $duration);
                $sheet->setCellValue('H7', 'Section: ' . ($student->schoolSection->section_name ?? '—'));

                // ── Row 8: Print Date | Session ──
                $sheet->setCellValue('A8', 'Print Date: ' . Carbon::now()->format('j-F-Y h:i A'));
                $sheet->setCellValue('H8', 'Session: ' . ($student->schoolSession->session_year ?? '—'));

                // ── Column widths ──
                $sheet->getColumnDimension('A')->setWidth(16);
                $sheet->getColumnDimension('B')->setWidth(14);
                $sheet->getColumnDimension('C')->setWidth(16);
                $sheet->getColumnDimension('D')->setWidth(14);
                $sheet->getColumnDimension('E')->setWidth(16);
                $sheet->getColumnDimension('F')->setWidth(12);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(12);
                $sheet->getColumnDimension('I')->setWidth(12);
                $sheet->getColumnDimension('J')->setWidth(12);
                $sheet->getColumnDimension('K')->setWidth(10);

                // ── Bold headings (now at row 11 after insert) ──
                $headingRow = 11;
                $sheet->getStyle("A{$headingRow}:{$lastCol}{$headingRow}")
                    ->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle("A{$headingRow}:{$lastCol}{$headingRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // ── Border for heading row ──
                $sheet->getStyle("A{$headingRow}:{$lastCol}{$headingRow}")
                    ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);

                // ── Apply status colors (now data starts at row 12) ──
                $colors = [
                    'Paid'              => 'FF16A34A',
                    'Partial Paid'      => 'FF2563EB',
                    'Due'               => 'FFEF4444',
                    'Due Partial'       => 'FFF97316',
                    'Over Due'          => 'FFB91C1C',
                    'Over Due Partial'  => 'FF9333EA',
                    'Advance'           => 'FF0D9488',
                    'Advance Partial'   => 'FF0891B2',
                ];
                $highestRow = $sheet->getHighestRow();
                for ($row = 12; $row <= $highestRow; $row++) {
                    $status = $sheet->getCell('E' . $row)->getValue();
                    if (isset($colors[$status])) {
                        $sheet->getStyle('E' . $row)
                            ->getFont()
                            ->setColor(new Color($colors[$status]))
                            ->setBold(true);
                    }
                }
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    private function getStatus($payDate, $total, $paid)
    {
        $due = max((float)$total - (float)$paid, 0);
        $d = $payDate ? Carbon::parse($payDate) : null;
        $today = Carbon::today();
        $isFuture = $d && $d->copy()->startOfDay()->gt($today);
        $isOverdue = $d && $due > 0 && $d->copy()->startOfDay()->lt($today);

        if ((float)$paid >= (float)$total && (float)$total > 0) {
            return $isFuture ? 'Advance' : 'Paid';
        }
        if ((float)$paid > 0 && (float)$paid < (float)$total) {
            if ($isFuture) return 'Advance Partial';
            if ($isOverdue) return 'Over Due Partial';
            return 'Partial Paid';
        }
        if ($isOverdue) return 'Over Due';
        return 'Due';
    }
}
