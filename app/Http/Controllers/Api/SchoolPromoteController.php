<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdmissionStudent;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolGroup;
use App\Models\SchoolSection;
use App\Models\SchoolSession;
use App\Models\SchoolFeeTemplate;
use App\Models\SchoolStudentFee;
use App\Models\StudentPromotion;
use App\Models\User;
use App\Models\StudentAcademicRecord;
use App\Services\SmsService;
use App\Services\SchoolStudentFeeGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SchoolPromoteController extends Controller
{
    protected $smsService;
    protected $feeGenerationService;

    public function __construct(SmsService $smsService, SchoolStudentFeeGenerationService $feeGenerationService)
    {
        $this->smsService = $smsService;
        $this->feeGenerationService = $feeGenerationService;
    }

    /**
     * Fetch students for Step-2 table selection
     */
    public function getStudents(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:school_classes,id',
            'group_id'   => 'nullable|exists:school_groups,id',
            'section_id' => 'required|exists:school_sections,id',
            'session_id' => 'required|exists:school_sessions,id',
        ]);

        $schoolUser = Auth::user();
        $schoolName = $schoolUser->school_name;

        $students = AdmissionStudent::where(function ($q) use ($schoolUser, $schoolName) {
            $q->where('school_id', $schoolUser->id)
              ->orWhere('school', $schoolName);
        })
        // Modified on 2026-07-07: Exclude Inactive students from promotion candidates
        ->where('status', '!=', 'Inactive')
        ->where('class', $request->class_id)
        ->where('section', $request->section_id)
        ->where('session', $request->session_id)
        ->when(
            $request->filled('group_id'),
            fn ($q) => $q->where('group', $request->group_id),
            fn ($q) => $q->whereNull('group')
        )
        ->orderBy('student_name', 'asc')
        ->get(['id', 'student_name', 'student_id_number']);

        return response()->json([
            'status' => 'success',
            'data' => $students
        ]);
    }

    /**
     * Execute Student Promotion
     */
    public function promote(Request $request)
    {
        $request->validate([
            'student_ids'   => 'required|array|min:1',
            'student_ids.*' => 'required|exists:admission_students,id',
            'to_class'      => 'required|exists:school_classes,id',
            'to_group'      => 'nullable|exists:school_groups,id',
            'to_section'    => 'required|exists:school_sections,id',
            'to_session'    => 'required|exists:school_sessions,id',
            'promote_date'  => 'required|date',
            'promote_fee'   => 'required|numeric|min:0',
        ]);

        $schoolUser = Auth::user();
        $school = School::where('user_id', $schoolUser->id)->first();
        if (!$school) {
            return response()->json(['message' => 'School profile not found.'], 400);
        }

        // Enforce: Promote Fee Template must exist
        $templateExists = SchoolFeeTemplate::where('school_id', $school->id)
            ->where('class_id', $request->to_class)
            ->where('session_id', $request->to_session)
            ->where(function ($q) {
                $q->where('fee_type_name', 'like', '%Promote%')
                  ->orWhere('fee_name', 'like', '%Promote%');
            })
            ->where('is_active', true)
            ->exists();

        if (!$templateExists) {
            return response()->json([
                'message' => 'Promotion is not allowed. No active Promote Fee Template exists for the destination class and session. Please create one first.'
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $schoolUser, $school) {
                // Pre-resolve destination descriptions for SMS template
                $classObj = SchoolClass::find($request->to_class);
                $groupObj = $request->to_group ? SchoolGroup::find($request->to_group) : null;
                $sectionObj = SchoolSection::find($request->to_section);
                $sessionObj = SchoolSession::find($request->to_session);

                $toClassName = $classObj?->class_name ?? 'N/A';
                $toGroupName = $groupObj?->group_name ?? 'n/a';
                $toSectionName = $sectionObj?->section_name ?? 'n/a';
                $toSessionYear = $sessionObj?->session_year ?? 'N/A';

                // Prepare Dynamic Admission ID Generation Prefix
                $schoolPrefix = substr($schoolUser->id_number, -5);
                $sessionYear = $sessionObj?->session_year ?? '0000';
                $lastAdmission = AdmissionStudent::where('school_id', $schoolUser->id)
                    ->where('admission_id', 'LIKE', $schoolPrefix . $sessionYear . '%')
                    ->orderBy('admission_id', 'desc')
                    ->first();

                $currentAdmSerial = $lastAdmission?->admission_id
                    ? (int) substr($lastAdmission->admission_id, -4)
                    : 0;

                foreach ($request->student_ids as $sid) {
                    $student = AdmissionStudent::where('school_id', $schoolUser->id)->findOrFail($sid);

                    // Generate a new dynamic Admission ID
                    $currentAdmSerial++;
                    $newAdmissionId = $schoolPrefix . $sessionYear . str_pad($currentAdmSerial, 4, '0', STR_PAD_LEFT);

                    // Save History
                    // Modified on 2026-07-09: Log dynamic admission IDs in promotion logs while student_id_number stays permanent
                    StudentPromotion::create([
                        'school_id' => $school->id,
                        'student_id' => $student->id,
                        'from_class_id' => $student->class,
                        'from_group_id' => $student->group,
                        'from_section_id' => $student->section,
                        'from_session_id' => $student->session,
                        'from_student_id_number' => $student->student_id_number,
                        'to_class_id' => $request->to_class,
                        'to_group_id' => $request->to_group,
                        'to_section_id' => $request->to_section,
                        'to_session_id' => $request->to_session,
                        'to_student_id_number' => $student->student_id_number, // Permanent Student ID stays unchanged
                        'from_admission_id' => $student->admission_id ?? $student->student_id_number,
                        'to_admission_id' => $newAdmissionId,
                        'promote_date' => $request->promote_date,
                        'promote_fee' => $request->promote_fee,
                    ]);

                    // Update Student Record (Promote to next class/session)
                    // Modified on 2026-07-09: Keep student_id_number unchanged, update dynamic admission_id
                    $student->update([
                        'class' => $request->to_class,
                        'group' => $request->to_group,
                        'section' => $request->to_section,
                        'session' => $request->to_session,
                        'admission_id' => $newAdmissionId,
                    ]);

                    // Create new StudentAcademicRecord for promoted session
                    StudentAcademicRecord::firstOrCreate(
                        [
                            'school_id'    => $schoolUser->id,
                            'student_id'   => $student->id,
                            'session_year' => $toSessionYear,
                        ],
                        [
                            'session_id' => $request->to_session,
                            'class_id'   => $request->to_class,
                            'group_id'   => $request->to_group,
                            'section_id' => $request->to_section,
                            'roll_no'    => $student->roll_no,
                            'status'     => 'Active',
                        ]
                    );

                    // Auto-generate Promote Fee via service
                    $this->feeGenerationService->generatePromoteFee(
                        $student,
                        $request->to_class,
                        $request->to_session,
                        $school->id
                    );

                    // Send Promote SMS
                    $defaultMessage = "Dear {student_name}\n{school_name}\nYour promote has been successful.\nDate : {date}\nClass : {class_name}\nGroup : {group_name}\nSection : {section_name}\nSession : {session_year}\nStudent ID : {student_id}\nAdmission ID : {admission_id}\nPassword : {password}\nPromote Fee : {fee_amount}\nPlease Do Not Share ID & Password.";

                    $this->smsService->triggerEventSms($school, $student->mobile, 'promotion', [
                        '{student_name}' => $student->student_name,
                        '{school_name}' => $school->school_name,
                        '{date}' => $request->promote_date,
                        '{class_name}' => $toClassName,
                        '{group_name}' => $toGroupName,
                        '{section_name}' => $toSectionName,
                        '{session_year}' => $toSessionYear,
                        '{student_id}' => $student->student_id_number,
                        '{admission_id}' => $newAdmissionId,
                        '{password}' => '00000000',
                        '{fee_amount}' => $request->promote_fee
                    ], $defaultMessage);
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Students promoted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Promotion Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to process student promotion.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch promotion history log for the current school
     * Added on 2026-07-07
     */
    public function getPromotionHistory(Request $request)
    {
        $schoolUser = Auth::user();
        $school = School::where('user_id', $schoolUser->id)->first();
        if (!$school) {
            return response()->json(['message' => 'School profile not found.'], 400);
        }

        $query = StudentPromotion::with([
            'student:id,student_name,status,inactive_date',
            'fromClass:id,class_name',
            'toClass:id,class_name',
            'fromSession:id,session_year',
            'toSession:id,session_year',
            'fromGroup:id,group_name',
            'toGroup:id,group_name',
            'fromSection:id,section_name',
            'toSection:id,section_name',
        ])
        ->where('school_id', $school->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sub) use ($search) {
                    $sub->where('student_name', 'like', "%{$search}%");
                })
                ->orWhere('from_student_id_number', 'like', "%{$search}%")
                ->orWhere('to_student_id_number', 'like', "%{$search}%");
            });
        }

        $history = $query->orderBy('id', 'desc')
            ->paginate($request->input('per_page', 30));

        $history->through(function ($item) {
            return [
                'id' => $item->id,
                'student_id' => $item->student_id,
                'status' => $item->student?->status,
                'inactive_date' => $item->student?->inactive_date,
                'student_name' => $item->student?->student_name,
                'from_student_id_number' => $item->from_student_id_number,
                'to_student_id_number' => $item->to_student_id_number,
                'from_admission_id' => $item->from_admission_id,
                'to_admission_id' => $item->to_admission_id,
                'from_class' => $item->fromClass?->class_name,
                'to_class' => $item->toClass?->class_name,
                'from_session' => $item->fromSession?->session_year,
                'to_session' => $item->toSession?->session_year,
                'from_group' => $item->fromGroup?->group_name,
                'to_group' => $item->toGroup?->group_name,
                'from_section' => $item->fromSection?->section_name,
                'to_section' => $item->toSection?->section_name,
                'promote_date' => $item->promote_date,
                'promote_fee' => $item->promote_fee,
            ];
        });

        return response()->json($history);
    }
}
