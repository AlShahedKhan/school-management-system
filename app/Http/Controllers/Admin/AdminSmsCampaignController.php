<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\AdminSmsTemplate;

use App\Models\AdminSmsActivation;

use App\Models\School;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

use App\Enums\SmsType;

use App\Enums\SmsChannel;

class AdminSmsCampaignController extends Controller
{
    private $defaults = [
        'admission' => "Dear {student_name}\n{school_name}\nYour admission has been successful.\nDate : {date}\nClass : {class_name}\nGroup : {group_name}\nSection : {section_name}\nSession : {session_year}\nStudent ID : {student_id}\nAdmission ID : {admission_id}\nPassword : {password}\nAdmission Fee : {fee_amount}\nPlease Do Not Share ID & Password.",

        'readmission' => "Dear {student_name}\n{school_name}\nYour re-admission has been successful.\nDate : {date}\nClass : {class_name}\nGroup : {group_name}\nSection : {section_name}\nSession : {session_year}\nStudent ID : {student_id}\nAdmission ID : {admission_id}\nPassword : {password}\nRe-Admission Fee : {fee_amount}\nPlease Do Not Share ID & Password.",

        'promotion' => "Dear {student_name}\n{school_name}\nYour promote has been successful.\nDate : {date}\nClass : {class_name}\nGroup : {group_name}\nSection : {section_name}\nSession : {session_year}\nStudent ID : {student_id}\nAdmission ID : {admission_id}\nPassword : {password}\nPromote Fee : {fee_amount}\nPlease Do Not Share ID & Password.",

        'teacher_registration' => "Welcome! Your registration at {school_name} is confirmed. Teacher ID: {teacher_id}. You can now login to your portal. Regards, {school_name}.",

        'income' => "জনাব, {student_name}\nআপনি {school_name}-এ {income_source} বাবদ {month} মাস {year} সাল এর {fee_amount} টাকা প্রদান করেছেন। ধন্যবাদ।",
    ];

    public function templatesIndex()
    {
        foreach ($this->defaults as $type => $body) {
            AdminSmsTemplate::firstOrCreate(
                ['sms_type' => SmsType::tryFrom($type), 'is_default' => true],
                ['title' => 'Default ' . ucfirst(str_replace('_', ' ', $type)), 'template_body' => $body]
            );
        }

        $defaultTemplates = AdminSmsTemplate::where('is_default', true)->get();

        $customTemplates = AdminSmsTemplate::where('is_default', false)->with('schools')->get();

        $schools = School::orderBy('school_name', 'asc')->get();

        return view('admin.sms-templates', compact('defaultTemplates', 'customTemplates', 'schools'));
    }

    public function saveTemplate(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer|exists:admin_sms_templates,id',

            'title' => 'required|string|max:255',

            'sms_type' => 'required|string',

            'template_body' => 'required|string|max:1000',

            'is_default' => 'required|boolean',

            'school_ids' => 'nullable|array',

            'school_ids.*' => 'integer|exists:schools,id',
        ]);

        try {
            DB::beginTransaction();

            $template = AdminSmsTemplate::updateOrCreate(
                ['id' => $validated['id'] ?? null],
                [
                    'title' => $validated['title'],

                    'sms_type' => SmsType::tryFrom($validated['sms_type']),

                    'template_body' => $validated['template_body'],

                    'is_default' => $validated['is_default'],
                ]
            );

            match ((bool) $validated['is_default']) {
                false => $template->schools()->sync($request->input('school_ids', [])),

                true => null
            };

            DB::commit();

            return response()->json([
                'status' => 'success',

                'message' => 'Template saved successfully!',

                'data' => $template
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Save Template Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',

                'message' => 'Failed to save template: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyTemplate($id)
    {
        $template = AdminSmsTemplate::findOrFail($id);

        if ($template->is_default) {
            return response()->json([
                'status' => 'error',

                'message' => 'Default templates cannot be deleted.'
            ], 400);
        }

        $template->delete();

        return response()->json([
            'status' => 'success',

            'message' => 'Template deleted successfully!'
        ]);
    }

    public function activationsIndex()
    {
        $activations = AdminSmsActivation::with(['school', 'template'])->get();

        $templates = AdminSmsTemplate::all();

        return view('admin.sms-activations', compact('activations', 'templates'));
    }

    public function saveActivation(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer|exists:admin_sms_activations,id',

            'school_id' => 'required|integer|exists:schools,id',

            'sms_type' => 'required|string',

            'admin_sms_template_id' => 'nullable|integer|exists:admin_sms_templates,id',

            'start_time' => 'required|string',

            'end_time' => 'required|string',

            'schedule_type' => 'required|string|in:single,daily,multiple',

            'schedule_dates' => 'nullable|array',

            'send_channel' => 'required|string',

            'is_active' => 'required|boolean',
        ]);

        $duplicate = match (empty($request->input('id'))) {
            true => AdminSmsActivation::where('school_id', $validated['school_id'])
                ->where('sms_type', SmsType::tryFrom($validated['sms_type'])->value)
                ->exists(),

            false => false
        };

        if ($duplicate) {
            return response()->json([
                'status' => 'error',

                'message' => 'This school already has an active setup for the selected SMS type.'
            ], 422);
        }

        $activation = AdminSmsActivation::updateOrCreate(
            ['id' => $validated['id'] ?? null],
            [
                'school_id' => $validated['school_id'],

                'sms_type' => SmsType::tryFrom($validated['sms_type']),

                'admin_sms_template_id' => $validated['admin_sms_template_id'],

                'start_time' => $validated['start_time'],

                'end_time' => $validated['end_time'],

                'schedule_type' => $validated['schedule_type'],

                'schedule_dates' => $request->input('schedule_dates', []),

                'send_channel' => SmsChannel::tryFrom($validated['send_channel']),

                'is_active' => $validated['is_active'],
            ]
        );

        return response()->json([
            'status' => 'success',

            'message' => 'SMS Activation saved successfully!',

            'data' => $activation
        ]);
    }

    public function destroyActivation($id)
    {
        $activation = AdminSmsActivation::findOrFail($id);

        $activation->delete();

        return response()->json([
            'status' => 'success',

            'message' => 'SMS Activation deleted successfully!'
        ]);
    }

    public function toggleActivation(Request $request, $id)
    {
        $activation = AdminSmsActivation::findOrFail($id);

        $activation->is_active = $request->input('is_active');

        $activation->save();

        return response()->json([
            'status' => 'success',

            'message' => 'Activation status updated!'
        ]);
    }

    public function getCountries()
    {
        $countries = School::whereNotNull('country')
            ->distinct()
            ->pluck('country');

        return response()->json($countries);
    }

    public function getDivisions(Request $request)
    {
        $divisions = School::where('country', $request->country)
            ->whereNotNull('division')
            ->distinct()
            ->pluck('division');

        return response()->json($divisions);
    }

    public function getDistricts(Request $request)
    {
        $districts = School::where('country', $request->country)
            ->where('division', $request->division)
            ->whereNotNull('district')
            ->distinct()
            ->pluck('district');

        return response()->json($districts);
    }

    public function getUpazilas(Request $request)
    {
        $upazilas = School::where('country', $request->country)
            ->where('division', $request->division)
            ->where('district', $request->district)
            ->whereNotNull('upazila')
            ->distinct()
            ->pluck('upazila');

        return response()->json($upazilas);
    }

    public function getSchools(Request $request)
    {
        $schools = School::where('country', $request->country)
            ->where('division', $request->division)
            ->where('district', $request->district)
            ->where('upazila', $request->upazila)
            ->get();

        return response()->json($schools);
    }
}
