<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Guardian;
use App\Models\AdmissionStudent;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolGroup;
use App\Models\SchoolSession;
use App\Models\SchoolFeeTemplate;
use App\Services\SchoolStudentFeeGenerationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdmissionController extends Controller
{
    protected $feeGenerationService;

    public function __construct(SchoolStudentFeeGenerationService $feeGenerationService)
    {
        $this->feeGenerationService = $feeGenerationService;
    }

    /* ============================================================
        DROPDOWN DATA FETCHING METHODS
    ============================================================ */

    public function getApprovedSchools()
    {
        $schools = School::where('approval_status', 'approved')
            ->select('id', 'school_name')
            ->get();
        return response()->json($schools);
    }

    public function getClasses($school_id)
    {
        $classes = SchoolClass::where('school_id', $school_id)
            ->select('id', 'class_name')
            ->get();
        return response()->json($classes);
    }

    public function getGroups($school_id, $class_id)
    {
        $groups = SchoolGroup::where('school_id', $school_id)
            ->where('class_id', $class_id)
            ->select('id', 'group_name')
            ->get();
        return response()->json($groups);
    }

    public function getSessions($school_id, $class_id)
    {
        $sessions = SchoolSession::active()
            ->where('school_id', $school_id)
            ->where('class_id', $class_id)
            ->select('id', 'session_year')
            ->get();
        return response()->json($sessions);
    }

    public function getFees($school_id, $class_id)
    {
        $fees = SchoolFeeTemplate::where('school_id', $school_id)
            ->where('class_id', $class_id)
            ->where('is_active', true)
            ->select('id', 'fee_type_name', 'fee_name', 'amount')
            ->get();
        return response()->json($fees);
    }

    /* ============================================================
        MAIN REGISTRATION LOGIC
    ============================================================ */

    public function register(Request $request)
    {
        $request->validate([
            // Step 1
            'a_division' => 'required',
            'a_district' => 'required',
            'a_upazila'  => 'required',
            'a_school'   => 'required', // This is the 'id' from schools table
            'a_class'    => 'required',
            'a_session'  => 'required',
            'a_fee'      => 'required',
            'a_date'     => 'required',

            // Step 3
            'g_name'     => 'required',
            'g_relation' => 'required',
            'g_mobile'   => 'required',

            // Step 4
            'student_name'   => 'required',
            'father_name'    => 'required',
            'mother_name'    => 'required',
            'student_mobile' => 'required|unique:users,mobile',
            'password'       => 'required|confirmed|min:6',
            'image'          => 'required|image'
        ]);

        // 1. Resolve School and find the OWNER (user_id)
        $school = School::find($request->a_school);
        if (!$school) {
            return response()->json(['message' => 'Selected school not found.'], 404);
        }

        // CRITICAL CHANGE: We store the school's user_id as the school_id for students
        $schoolOwnerUserId = $school->user_id;
        $schoolName = $school->school_name;

        // 2. Resolve Names for Class, Session, Group
        $class   = SchoolClass::find($request->a_class);
        $session = SchoolSession::find($request->a_session);
        $group   = SchoolGroup::find($request->a_group);

        $className   = $class ? $class->class_name : $request->a_class;
        $sessionYear = $session ? $session->session_year : $request->a_session;
        $groupName   = $group ? $group->group_name : $request->a_group;

        // Check admission deadline & template existence
        $admissionFeeTemplate = SchoolFeeTemplate::where('school_id', $school->id)
            ->where('class_id', $request->a_class)
            ->where('session_id', $request->a_session)
            ->where(function ($q) {
                $q->where('fee_type_name', 'Admission')
                  ->orWhereHas('assign', fn ($q) => $q->where('name', 'Admission'));
            })
            ->where('is_active', true)
            ->first(['id', 'pay_date']);

        if (!$admissionFeeTemplate) {
            return response()->json([
                'message' => 'Admission is not allowed. No active Admission Fee Template exists for this class and session.'
            ], 422);
        }

        if ($admissionFeeTemplate->pay_date) {
            $deadline = \Carbon\Carbon::parse($admissionFeeTemplate->pay_date);
            if ($deadline->isPast()) {
                return response()->json([
                    'message' => 'Admission deadline has passed (' . $deadline->format('d/m/Y') . '). Please contact the school admin.'
                ], 422);
            }
        }

        try {
            return DB::transaction(function () use ($request, $school, $schoolOwnerUserId, $schoolName, $className, $sessionYear, $groupName) {

                // 2. Create Guardian
                $guardian = Guardian::create([
                    'name'     => $request->g_name,
                    'relation' => $request->g_relation,
                    'division' => $request->g_division,
                    'district' => $request->g_district,
                    'upazila'  => $request->g_upazila,
                    'village'  => $request->g_village,
                    'mobile'   => $request->g_mobile,
                ]);

                // 3. Upload Student Image
                $imagePath = $request->file('image')->store('students', 'public');

                // 4. Save Admission Record
                // Modified on 2026-07-11: student_id_number will be auto-generated by AdmissionStudent booted observer
                $admission = AdmissionStudent::create([
                    'division'       => $request->a_division,
                    'district'       => $request->a_district,
                    'upazila'        => $request->a_upazila,
                    'school_id'      => $schoolOwnerUserId,
                    'school'         => $schoolName,
                    'class_id'       => $request->a_class,
                    'group_id'       => $request->a_group,
                    'section_id'     => $request->a_section,
                    'session_id'     => $request->a_session,
                    'admission_fee'  => $request->a_fee,
                    'admission_date' => $request->a_date,

                    'previous_school'  => $request->p_school,
                    'previous_class'   => $request->p_class,
                    'previous_group'   => $request->p_group,
                    'previous_section' => $request->p_section,
                    'previous_session' => $request->p_session,
                    'last_exam_result' => $request->last_exam_result,

                    'guardian_id'       => $guardian->id,
                    'student_name'      => $request->student_name,
                    'father_name'       => $request->father_name,
                    'mother_name'       => $request->mother_name,

                    'current_division' => $request->c_division,
                    'current_district' => $request->c_district,
                    'current_upazila'  => $request->c_upazila,
                    'current_village'  => $request->c_village,

                    'permanent_division' => $request->p_division2,
                    'permanent_district' => $request->p_district2,
                    'permanent_upazila'  => $request->p_upazila2,
                    'permanent_village'  => $request->p_village2,

                    'mobile'   => $request->student_mobile,
                    'password' => Hash::make('00000000'),
                    'image'    => $imagePath,
                    'status'   => 'pending'
                ]);

                // 5. Create Student User
                User::create([
                    'role'        => 'student',
                    'name'        => $request->student_name,
                    'school_id'   => $schoolOwnerUserId,
                    'school_name' => $schoolName,
                    'mobile'      => $request->student_mobile,
                    'id_number'   => $admission->student_id_number,
                    'password'    => Hash::make($request->password),
                ]);

                // Auto-generate Admission Fee
                $this->feeGenerationService->generateAdmissionFee(
                    $admission,
                    $request->a_class,
                    $request->a_session,
                    $school->id
                );

                // 6. Send SMS via MiMSMS Gateway
                $this->sendMiMSMS($request->student_mobile, $schoolName, $admission->student_id_number);

                return response()->json([
                    'redirect' => '/student/approval-status?id=' . $admission->student_id_number . '&name=' . urlencode($request->student_name),
                    'message'  => 'Registration successful.'
                ]);
            });
        } catch (\Exception $e) {
            Log::error("Registration Error: " . $e->getMessage());
            return response()->json(['message' => 'Registration failed. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper: MiMSMS Integration
     */
    private function sendMiMSMS($mobile, $schoolName, $studentId)
    {
        try {
            $phone = preg_replace('/[^0-9]/', '', $mobile);
            if (!str_starts_with($phone, '88')) {
                $phone = '88' . ltrim($phone, '0');
            }

            $message = "Your admission registration is successful. Student ID: $studentId. Please complete the payment manually & confirm your admission. Regards, $schoolName.";

            $baseUrl = rtrim(env('SMS_BASE_URL'), '/');

            Http::withoutVerifying()
                ->withHeaders(['Accept' => 'application/json', 'Content-Type' => 'application/json'])
                ->timeout(15)
                ->post("{$baseUrl}/api/SmsSending/SMS", [
                    "UserName"        => env('SMS_USERNAME'),
                    "Apikey"          => env('SMS_API_KEY'),
                    "MobileNumber"    => $phone,
                    "CampaignId"      => "null",
                    "SenderName"      => env('SMS_SENDER_ID'),
                    "TransactionType" => "T",
                    "Message"         => $message
                ]);
        } catch (\Exception $e) {
            Log::error("Admission SMS failed for $mobile: " . $e->getMessage());
        }
    }
}
