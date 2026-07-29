<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminSmsTemplate;
use App\Enums\SmsType;

class AdminSmsTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            'admission' => [
                'title' => 'Default Admission Confirmation',
                'body' => "Dear {student_name}\n{school_name}\nYour admission has been successful.\nDate : {date}\nClass : {class_name}\nGroup : {group_name}\nSection : {section_name}\nSession : {session_year}\nStudent ID : {id_number}\nAdmission Fee : {fee_amount}\nPlease Do Not Share ID & Password.",
            ],
            're_admission' => [
                'title' => 'Default Re-Admission Confirmation',
                'body' => "Dear {student_name}\n{school_name}\nYour re-admission has been successful.\nDate : {date}\nClass : {class_name}\nGroup : {group_name}\nSection : {section_name}\nSession : {session_year}\nStudent ID : {id_number}\nRe-Admission Fee : {fee_amount}\nPlease Do Not Share ID & Password.",
            ],
            'promote' => [
                'title' => 'Default Student Promotion',
                'body' => "Dear {student_name}\n{school_name}\nYour promotion has been successful.\nDate : {date}\nClass : {class_name}\nGroup : {group_name}\nSection : {section_name}\nSession : {session_year}\nStudent ID : {id_number}\nPromote Fee : {fee_amount}\nPlease Do Not Share ID & Password.",
            ],
            'teacher_registration' => [
                'title' => 'Default Teacher Registration',
                'body' => "Welcome! Your registration at {school_name} is confirmed. Teacher ID: {teacher_id}. You can now login to your portal. Regards, {school_name}.",
            ],
            'fee_payment' => [
                'title' => 'Default Fee Payment Confirmation',
                'body' => "Dear {student_name},\n{school_name}\nPayment Received! Paid Amount: TK {paid_amount} for ({fee_type}). Receipt No: {receipt_no}. Date: {date}. Thank you.",
            ],
        ];

        foreach ($templates as $typeKey => $data) {
            $smsEnum = SmsType::tryFrom($typeKey);
            AdminSmsTemplate::updateOrCreate(
                [
                    'sms_type' => $smsEnum ?: $typeKey,
                    'is_default' => true,
                ],
                [
                    'title' => $data['title'],
                    'template_body' => $data['body'],
                ]
            );
        }
    }
}
