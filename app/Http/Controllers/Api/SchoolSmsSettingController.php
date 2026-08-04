<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolSmsSetting;
use App\Models\AdminSmsTemplate;
use App\Models\School;
use App\Models\Teacher;
use App\Enums\SmsType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SchoolSmsSettingController extends Controller
{
    private array $defaults = [
        'admission' => [
            'title' => 'Default Admission',
            'body' => "Dear {student_name}\n{school_name}\nYour admission has been successful.\nDate : {date}\nClass : {class_name}\nGroup : {group_name}\nSection : {section_name}\nSession : {session_year}\nStudent ID : {student_id}\nAdmission ID : {admission_id}\nPassword : {password}\nAdmission Fee : {fee_amount}\nPlease Do Not Share ID & Password.",
        ],
        'readmission' => [
            'title' => 'Default Readmission',
            'body' => "Dear {student_name}\n{school_name}\nYour re-admission has been successful.\nDate : {date}\nClass : {class_name}\nGroup : {group_name}\nSection : {section_name}\nSession : {session_year}\nStudent ID : {student_id}\nAdmission ID : {admission_id}\nPassword : {password}\nRe-Admission Fee : {fee_amount}\nPlease Do Not Share ID & Password.",
        ],
        'promotion' => [
            'title' => 'Default Promotion',
            'body' => "Dear {student_name}\n{school_name}\nYour promote has been successful.\nDate : {date}\nClass : {class_name}\nGroup : {group_name}\nSection : {section_name}\nSession : {session_year}\nStudent ID : {student_id}\nAdmission ID : {admission_id}\nPassword : {password}\nPromote Fee : {fee_amount}\nPlease Do Not Share ID & Password.",
        ],
        'teacher_registration' => [
            'title' => 'Default Teacher registration',
            'body' => "Welcome! Your registration at {school_name} is confirmed. Teacher ID: {teacher_id}. You can now login to your portal. Regards, {school_name}.",
        ],
        'income' => [
            'title' => 'Default Income',
            'body' => "জনাব, {student_name}\nআপনি {school_name}-এ {income_source} বাবদ {month} মাস {year} সাল এর {fee_amount} টাকা প্রদান করেছেন। ধন্যবাদ।",
        ],
    ];

    private array $placeholdersMap = [
        'admission' => ['{student_name}', '{school_name}', '{date}', '{class_name}', '{group_name}', '{section_name}', '{session_year}', '{student_id}', '{admission_id}', '{id_number}', '{password}', '{fee_amount}', '{mobile}'],
        'readmission' => ['{student_name}', '{school_name}', '{date}', '{class_name}', '{group_name}', '{section_name}', '{session_year}', '{student_id}', '{admission_id}', '{id_number}', '{password}', '{fee_amount}', '{mobile}'],
        'promotion' => ['{student_name}', '{school_name}', '{date}', '{class_name}', '{group_name}', '{section_name}', '{session_year}', '{student_id}', '{admission_id}', '{id_number}', '{password}', '{fee_amount}', '{mobile}'],
        'teacher_registration' => ['{teacher_name}', '{teacher_id}', '{school_name}', '{mobile}'],
        'income' => ['{student_name}', '{school_name}', '{income_source}', '{month}', '{year}', '{fee_amount}', '{date}', '{paid_amount}', '{fee_type}', '{receipt_no}'],
    ];

    private function ensureDefaultTemplatesExist(): void
    {
        foreach ($this->defaults as $type => $data) {
            $enumType = SmsType::tryFrom($type);
            if ($enumType) {
                AdminSmsTemplate::firstOrCreate(
                    ['sms_type' => $enumType, 'is_default' => true],
                    ['title' => $data['title'], 'template_body' => $data['body']]
                );
            }
        }
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

        $this->ensureDefaultTemplatesExist();

        $adminTemplates = AdminSmsTemplate::where(function ($query) use ($schoolId) {
            $query->where('is_default', true)
                ->orWhere(function ($customQuery) use ($schoolId) {
                    $customQuery->where('is_default', false)
                        ->where(function ($sq) use ($schoolId) {
                            $sq->whereDoesntHave('schools')
                                ->orWhereHas('schools', function ($schoolRelation) use ($schoolId) {
                                    $schoolRelation->where('schools.id', $schoolId);
                                });
                        });
                });
        })->get();

        $savedSettings = SchoolSmsSetting::where('school_id', $schoolId)->get()->keyBy('sms_type');

        $result = [];
        foreach ($adminTemplates as $template) {
            $typeKey = $template->sms_type instanceof \BackedEnum ? $template->sms_type->value : (string) $template->sms_type;

            $saved = $savedSettings->get($typeKey);

            preg_match_all('/\{[a-zA-Z0-9_]+\}/', $template->template_body, $matches);
            $bodyPlaceholders = $matches[0] ?? [];
            $basePlaceholders = $this->placeholdersMap[$typeKey] ?? [];
            $combinedPlaceholders = array_values(array_unique(array_merge($basePlaceholders, $bodyPlaceholders)));

            $result[] = [
                'id' => $template->id,
                'type' => $typeKey,
                'label' => $template->title ?: ucfirst(str_replace('_', ' ', $typeKey)),
                'placeholders' => $combinedPlaceholders,
                'default_body' => $template->template_body,
                'current_body' => $saved?->template_body ?? $template->template_body,
                'status' => $saved?->status ?? 'Active',
                'is_customized' => $saved !== null && !empty($saved->template_body),
                'is_default' => (bool) $template->is_default,
            ];
        }

        return response()->json([
            'types' => $result,
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

        $smsType = match ($request->sms_type) {
            're_admission' => 'readmission',
            'promote' => 'promotion',
            'fee_payment' => 'income',
            default => $request->sms_type
        };

        $setting = SchoolSmsSetting::updateOrCreate(
            [
                'school_id' => $schoolId,
                'sms_type' => $smsType,
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
