<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolSmsSetting;
use App\Models\AdminSmsTemplate;
use App\Models\School;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SchoolSmsSettingController extends Controller
{
    private function getAvailableTypes(): array
    {
        return [
            'admission' => [
                'label' => 'Admission Confirmation',
                'placeholders' => ['{student_name}', '{school_name}', '{admission_id}', '{id_number}', '{class_name}', '{section_name}', '{session_year}', '{mobile}'],
                'default_body' => 'Welcome {student_name}! Your admission to {school_name} (ID: {student_id_number}) is successful. Class: {class_name}, Section: {section_name}.',
            ],
            're_admission' => [
                'label' => 'Re-Admission',
                'placeholders' => ['{student_name}', '{school_name}', '{class_name}', '{section_name}', '{session_year}'],
                'default_body' => 'Dear {student_name}, your re-admission to {school_name} for class {class_name} ({session_year}) is completed successfully.',
            ],
            'promote' => [
                'label' => 'Student Promotion',
                'placeholders' => ['{student_name}', '{school_name}', '{class_name}', '{section_name}', '{session_year}'],
                'default_body' => 'Congratulations {student_name}! You have been promoted to class {class_name} ({session_year}) at {school_name}.',
            ],
            'teacher_registration' => [
                'label' => 'Teacher Registration',
                'placeholders' => ['{teacher_name}', '{teacher_id}', '{school_name}', '{mobile}'],
                'default_body' => 'Welcome {teacher_name}! Your registration at {school_name} is confirmed. Teacher ID: {teacher_id}.',
            ],
            'fee_payment' => [
                'label' => 'Fee Payment Confirmation',
                'placeholders' => ['{student_name}', '{paid_amount}', '{fee_type}', '{receipt_no}', '{date}', '{school_name}'],
                'default_body' => 'Payment Received! Paid Amount: TK {paid_amount} for {student_name} ({fee_type}). Date: {date}. Thank you, {school_name}.',
            ],
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $schoolId = match ($user->role) {
            'teacher' => (Teacher::where('id_number', $user->id_number)->first()?->school_id),
            default => (School::where('user_id', $user->id)->value('id') ?? $user->id),
        };

        $available = $this->getAvailableTypes();
        $savedSettings = SchoolSmsSetting::where('school_id', $schoolId)->get()->keyBy('sms_type');

        $result = [];
        foreach ($available as $type => $info) {
            $saved = $savedSettings->get($type);

            // Fetch admin default template if exists
            $adminDefault = AdminSmsTemplate::where('sms_type', $type)
                ->where('is_default', true)
                ->value('template_body');

            $defaultBody = $adminDefault ?: $info['default_body'];

            $result[$type] = [
                'type' => $type,
                'label' => $info['label'],
                'placeholders' => $info['placeholders'],
                'default_body' => $defaultBody,
                'current_body' => $saved?->template_body ?? $defaultBody,
                'status' => $saved?->status ?? 'Active',
                'is_customized' => $saved !== null && $saved->template_body !== null,
            ];
        }

        return response()->json([
            'types' => array_values($result),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $schoolId = match ($user->role) {
            'teacher' => (Teacher::where('id_number', $user->id_number)->first()?->school_id),
            default => (School::where('user_id', $user->id)->value('id') ?? $user->id),
        };

        $validator = Validator::make($request->all(), [
            'sms_type' => 'required|string',
            'template_body' => 'required|string',
            'status' => 'required|in:Active,Inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $setting = SchoolSmsSetting::updateOrCreate(
            [
                'school_id' => $schoolId,
                'sms_type' => $request->sms_type,
            ],
            [
                'template_body' => $request->template_body,
                'status' => $request->status,
            ]
        );

        return response()->json([
            'message' => 'SMS Settings saved successfully',
            'setting' => $setting,
        ]);
    }
}
