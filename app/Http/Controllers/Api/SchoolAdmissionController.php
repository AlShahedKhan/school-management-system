<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Guardian;
use App\Models\AdmissionStudent;
use App\Models\SchoolClass;
use App\Models\School;
use App\Models\SchoolFeeTemplate;
use App\Services\SmsService;
use App\Services\SchoolStudentFeeGenerationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SchoolAdmissionController extends Controller
{
    protected $smsService;
    protected $feeGenerationService;

    public function __construct(SmsService $smsService, SchoolStudentFeeGenerationService $feeGenerationService)
    {
        $this->smsService = $smsService;
        $this->feeGenerationService = $feeGenerationService;
    }

    public function register(Request $request)
    {
        // 1. Validation
        try {
            $request->validate([
                'a_class' => 'required|exists:school_classes,id',
                'a_section' => 'required|exists:school_sections,id',
                'a_session' => 'required|exists:school_sessions,id',
                'a_group' => 'nullable|exists:school_groups,id',
                'a_fee' => 'required',
                'a_date' => 'required|date',
                'g_type'     => 'required|in:Father,Mother,Other',
                'g_name'     => 'required|string|max:255',
                'g_relation' => 'nullable|string|max:255',
                'g_mobile'   => 'required',
                'student_name' => 'required|string|max:255',
                'father_name' => 'required|string|max:255',
                'mother_name' => 'required|string|max:255',
                'student_mobile' => 'required',
                'password' => 'nullable',
                'image' => 'nullable|image|max:2048',
                'current_division' => 'required',
                'current_district' => 'required',
                'current_upazila' => 'required',
                'current_village' => 'required',
                'permanent_division' => 'required',
                'permanent_district' => 'required',
                'permanent_upazila' => 'required',
                'permanent_village' => 'required',
                // Added on 2026-07-06: Validation for country fields
                'current_country' => 'nullable|string',
                'permanent_country' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $currentUser = Auth::user();
        $schoolId = $currentUser->id;

        // Enforce: Admission Fee Template must exist
        $templateExists = SchoolFeeTemplate::where('school_id', function ($q) use ($schoolId) {
            $q->select('id')->from('schools')->where('user_id', $schoolId)->limit(1);
        })
        ->where('class_id', $request->a_class)
        ->where('session_id', $request->a_session)
        ->where(function ($q) {
            $q->where('fee_type_name', 'Admission')
              ->orWhereHas('assign', fn ($q) => $q->where('name', 'Admission'));
        })
        ->where('is_active', true)
        ->exists();

        if (!$templateExists) {
            return response()->json([
                'message' => 'Admission is not allowed. No active Admission Fee Template exists for this class and session. Please create one first.'
            ], 422);
        }

        try {
            // Use DB Transaction to ensure data integrity
            $result = DB::transaction(function () use ($request, $schoolId) {

                $currentUser = Auth::user();
                $schoolName = $currentUser->school_name;

                if (!$schoolId) {
                    throw new \Exception("Authentication error: School ID not found.");
                }

                // --- CONVERT IDs TO ACTUAL VALUES FOR SMS/LOGS ONLY ---
                $classObj = SchoolClass::find($request->a_class);
                $classNameForSms = $classObj ? $classObj->class_name : 'N/A';

                // 3. Determine guardian relation
                $guardianRelation = $request->g_type;
                if ($guardianRelation === 'Other') {
                    $guardianRelation = $request->g_relation ?? 'N/A';
                }

                // 4. Create Guardian (uses student's permanent address)
                $guardian = Guardian::create([
                    'name' => $request->g_name,
                    'relation' => $guardianRelation,
                    'division' => $request->permanent_division,
                    'district' => $request->permanent_district,
                    'upazila' => $request->permanent_upazila,
                    'village' => $request->permanent_village,
                    'mobile' => $request->g_mobile,
                ]);

                // 5. Handle Image Upload
                $imagePath = null;
                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('students', 'public');
                }

                // 6. Save Admission Student Record (STORING IDs INSTEAD OF NAMES)
                // Modified on 2026-07-09: Delegate student ID and admission ID generation to model observer
                $admission = AdmissionStudent::create([
                    'school_id' => $schoolId,
                    'school' => $schoolName,
                    'class_id' => $request->a_class,
                    'group_id' => $request->a_group,
                    'section_id' => $request->a_section,
                    'session_id' => $request->a_session,
                    'admission_fee' => $request->a_fee,
                    'admission_date' => $request->a_date,
                    'previous_school' => $request->p_school ?? 'n/a',
                    'previous_class' => $request->p_class ?? 'n/a',
                    'previous_group' => $request->p_group ?? 'n/a',
                    'previous_section' => $request->p_section ?? 'n/a',
                    'previous_session' => $request->p_session ?? 'n/a',
                    'last_exam_result' => $request->last_exam_result ?? 'n/a',
                    'guardian_id' => $guardian->id,
                    'student_name' => $request->student_name,
                    'father_name' => $request->father_name,
                    'mother_name' => $request->mother_name,
                    'dob' => $request->dob,
                    'nid_birth_certificate' => $request->nid_birth_certificate,
                    'blood_group' => $request->blood_group,
                    'mobile' => $request->student_mobile,
                    'password' => Hash::make('00000000'),
                    'image' => $imagePath,
                    'current_division' => $request->current_division,
                    'current_district' => $request->current_district,
                    'current_upazila' => $request->current_upazila,
                    'current_village' => $request->current_village,
                    'permanent_division' => $request->permanent_division,
                    'permanent_district' => $request->permanent_district,
                    'permanent_upazila' => $request->permanent_upazila,
                    'permanent_village' => $request->permanent_village,
                    // Added on 2026-07-06: Store country fields
                    'current_country' => $request->current_country,
                    'permanent_country' => $request->permanent_country,
                    // Modified on 2026-07-07: Use Active status by default for admitted students
                    'status' => 'Active',
                ]);

                // 6. Create Student User Account
                User::create([
                    'role' => 'student',
                    'name' => $request->student_name,
                    'school_name' => $schoolName,
                    'mobile' => $request->student_mobile,
                    'id_number' => $admission->student_id_number ?? '',
                    'password' => Hash::make('00000000'),
                ]);

                // Auto-generate Admission Fee
                $school = \App\Models\School::where('user_id', $schoolId)->first();
                $this->feeGenerationService->generateAdmissionFee(
                    $admission,
                    $request->a_class,
                    $request->a_session,
                    $school?->id
                );

                return [
                    'student_mobile' => $request->student_mobile,
                    'school_name'    => $schoolName,
                    'student_id'     => $admission->student_id_number ?? '',
                    'admission_id'   => $admission->admission_id ?? '',
                    'school_id'      => $schoolId,
                    'admission'      => $admission
                ];
            });

            // 7. Send SMS using Service
            $this->sendAdmissionSMS($result['admission'], $result['school_name'], $result['school_id']);

            return response()->json([
                'message' => 'Student registered and approved successfully! ID: ' . $result['student_id'] . ', Admission ID: ' . $result['admission_id'],
                'redirect' => '/school/students'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Admission Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred during registration.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper Method: Uses SmsService to send notification
     */
    private function sendAdmissionSMS($admission, $schoolName, $schoolUserId)
    {
        $school = School::where('user_id', $schoolUserId)->first();

        if (!$school) {
            Log::error("Admission SMS failed: School record not found for user ID $schoolUserId");
            return;
        }

        // Modified on 2026-07-06: format template to match requested Admission SMS format
        $admission->load(['schoolClass', 'schoolGroup', 'schoolSection', 'schoolSession']);

        $className   = $admission->schoolClass->class_name ?? $admission->class;
        $groupName   = $admission->schoolGroup->group_name ?? $admission->group ?? 'n/a';
        $sectionName = $admission->schoolSection->section_name ?? $admission->section ?? 'n/a';
        $sessionYear = $admission->schoolSession->session_year ?? $admission->session;

        $defaultMessage = "Dear {student_name}\n{school_name}\nYour admission has been successful.\nDate : {date}\nClass : {class_name}\nGroup : {group_name}\nSection : {section_name}\nSession : {session_year}\nStudent ID : {student_id}\nAdmission ID : {admission_id}\nPassword : {password}\nAdmission Fee : {fee_amount}\nPlease Do Not Share ID & Password.";

        $smsResponse = $this->smsService->triggerEventSms($school, $admission->mobile, 'admission', [
            '{student_name}' => $admission->student_name,
            '{school_name}' => $schoolName,
            '{date}' => $admission->admission_date,
            '{class_name}' => $className,
            '{group_name}' => $groupName,
            '{section_name}' => $sectionName,
            '{session_year}' => $sessionYear,
            '{student_id}' => $admission->student_id_number,
            '{admission_id}' => $admission->admission_id,
            '{password}' => '00000000',
            '{fee_amount}' => $admission->admission_fee
        ], $defaultMessage);

        if (!$smsResponse['success']) {
            Log::warning("Admission SMS for {$admission->mobile} failed or skipped: " . $smsResponse['message']);
        }
    }
}