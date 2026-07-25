<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolExpense;

class SchoolExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasons = [
            'Office Rent',
            'Teacher Salary',
            'Staff Salary',
            'Electricity Bill',
            'Water Bill',
            'Internet Bill',
            'Office Stationery',
            'Cleaning Materials',
            'Computer Maintenance',
            'Printer Ink Purchase',
            'Furniture Purchase',
            'Library Books',
            'Sports Equipment',
            'Laboratory Equipment',
            'School Decoration',
            'Transport Expense',
            'Fuel Expense',
            'Security Service',
            'Generator Fuel',
            'Generator Maintenance',
            'Building Maintenance',
            'Classroom Renovation',
            'CCTV Maintenance',
            'Air Conditioner Service',
            'Medical Supplies',
            'Student Welfare',
            'Examination Materials',
            'Photocopy Expense',
            'Software Subscription',
            'Website Hosting',
            'Domain Renewal',
            'Bank Charge',
            'Courier Service',
            'Internet Device Purchase',
            'Projector Maintenance',
            'Whiteboard Purchase',
            'Fan Repair',
            'Garden Maintenance',
            'Drinking Water',
            'Kitchen Expense',
            'Office Tea & Snacks',
            'Event Management',
            'Cultural Program',
            'Science Fair',
            'Admission Campaign',
            'Training Workshop',
            'Audit Expense',
            'Legal Consultancy',
            'Miscellaneous Expense',
            'Emergency Expense',
        ];
        $balance = 500000;
        for ($i = 1; $i <= 50; $i++) {
            $amount = rand(1000, 50000);
            $balance -= $amount;
            SchoolExpense::create([
                'school_id'      => 1,
                'invoice_no'     => str_pad($i, 8, '0', STR_PAD_LEFT),
                'expense_date'   => now()->subDays(50 - $i)->toDateString(),
                'expense_reason' => $reasons[$i - 1],
                'amount'         => $amount,
                'balance'        => max($balance, 0),
            ]);
        }
    }
}