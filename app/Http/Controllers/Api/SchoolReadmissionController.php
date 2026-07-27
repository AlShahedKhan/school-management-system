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
use App\Models\StudentReadmission;
use App\Models\User;
use App\Models\StudentAcademicRecord;
use App\Services\SmsService;
use App\Services\SchoolStudentFeeGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolReadmissionController extends Controller
{
    protected $smsService;
    protected $feeGenerationService;

    /**
     * Created on 2026-07-09: Controller for student same-class re-admission flow
     */
    public function __construct(SmsService $smsService, SchoolStudentFeeGenerationService $feeGenerationService)
    {
        $this->smsService = $smsService;
        $this->feeGenerationService = $feeGenerationService;
    }

    /**
     * Fetch students for Step-2 table selection (candidates for Re-Admission)
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
        ->where('status', '!=', 'Inactive')
        ->where('class_id', $request->class_id)
        ->where('section_id', $request->section_id)
        ->where('session_id', $request->session_id)
        ->when(
            $request->filled('group_id'),
            fn ($q) => $q->where('group_id', $request->group_id),
            fn ($q) => $q->where(fn($sq) => $sq->whereNull('group_id')->orWhere('group_id', ''))
        )
        ->orderBy('student_name', 'asc')
        ->get(['id', 'student_name', 'student_id_number', 'admission_id']);

        return response()->json([
            'status' => 'success',
            'data' => $students
        ]);
    }

    /**
     * Execute Student Re-Admission
     */
    public function readmit(Request $request)
    {
        $request->validate([
            'student_ids'   => 'required|array|min:1',
            'student_ids.*' => 'required|exists:admission_students,id',
            'to_group'      => 'nullable|exists:school_groups,id',
            'to_section'    => 'required|exists:school_sections,id',
            'to_session'    => 'required|exists:school_sessions,id',
            'readmit_date'  => 'required|date',
            'readmit_fee'   => 'required|numeric|min:0',
        ]);

        $schoolUser = Auth::user();
        $school = School::where('user_id', $schoolUser->id)->first();
        if (!$school) {
            return response()->json(['message' => 'School profile not found.'], 400);
        }

        // Enforce: Admission Fee Template must exist
        $firstStudent = AdmissionStudent::where('school_id', $schoolUser->id)->find($request->student_ids[0]);
        $classId = $firstStudent?->class_id;

        if ($classId) {
            $templateExists = SchoolFeeTemplate::where('school_id', $school->id)
                ->where('class_id', $classId)
                ->where('session_id', $request->to_session)
                ->where(function ($q) {
                    $q->where('fee_type_name', 'Admission')
                      ->orWhere('fee_type_name', 'like', '%Re-Admission%')
                      ->orWhereHas('assign', fn ($q) => $q->where('name', 'Admission'));
                })
                ->where('is_active', true)
                ->exists();

            if (!$templateExists) {
                return response()->json([
                    'message' => 'Re-Admission is not allowed. No active Admission Fee Template exists for this class and session. Please create one first.'
                ], 422);
            }
        }

        try {
            DB::transaction(function () use ($request, $schoolUser, $school) {
                // Pre-resolve destination descriptions for SMS template
                $sectionObj = SchoolSection::find($request->to_section);
                $sessionObj = SchoolSession::find($request->to_session);

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

                    // Re-resolve class description
                    $classObj = SchoolClass::find($student->class_id);
                    $className = $classObj?->class_name ?? 'N/A';

                    // Group details
                    $groupObj = $request->to_group ? SchoolGroup::find($request->to_group) : null;
                    $toGroupName = $groupObj?->group_name ?? 'n/a';

                    // Generate new dynamic Admission ID
                    $currentAdmSerial++;
                    $newAdmissionId = $schoolPrefix . $sessionYear . str_pad($currentAdmSerial, 4, '0', STR_PAD_LEFT);

                    // Save History
                    StudentReadmission::create([
                        'school_id' => $school->id,
                        'student_id' => $student->id,
                        'class_id' => $student->class_id,
                        'from_group_id' => $student->group_id,
                        'to_group_id' => $request->to_group,
                        'from_section_id' => $student->section_id,
                        'to_section_id' => $request->to_section,
                        'from_session_id' => $student->session_id,
                        'to_session_id' => $request->to_session,
                        'student_id_number' => $student->student_id_number,
                        'from_admission_id' => $student->admission_id ?? $student->student_id_number,
                        'to_admission_id' => $newAdmissionId,
                        'readmission_date' => $request->readmit_date,
                        'readmission_fee' => $request->readmit_fee,
                    ]);

                    // Update Student Record (Re-admit in same class, target session/section)
                    $student->update([
                        'group_id' => $request->to_group,
                        'section_id' => $request->to_section,
                        'session_id' => $request->to_session,
                        'admission_id' => $newAdmissionId,
                    ]);

                    // Create new StudentAcademicRecord for re-admitted session
                    $targetSessionObj = SchoolSession::find($request->to_session);
                    $targetSessionYear = $targetSessionObj?->session_year ?? $sessionYear;

                    StudentAcademicRecord::firstOrCreate(
                        [
                            'school_id'    => $schoolUser->id,
                            'student_id'   => $student->id,
                            'session_year' => $targetSessionYear,
                        ],
                        [
                            'session_id' => $request->to_session,
                            'class_id'   => $student->class_id,
                            'group_id'   => $request->to_group,
                            'section_id' => $request->to_section,
                            'roll_no'    => $student->roll_no,
                            'status'     => 'Active',
                        ]
                    );

                    // Auto-generate Admission Fee via service
                    $this->feeGenerationService->generateAdmissionFee(
                        $student,
                        $student->class_id,
                        $request->to_session,
                        $school->id
                    );

                    // Send Re-Admission SMS
                    $defaultMessage = "Dear {student_name}\n{school_name}\nYour re-admission has been successful.\nDate : {date}\nClass : {class_name}\nGroup : {group_name}\nSection : {section_name}\nSession : {session_year}\nStudent ID : {student_id}\nAdmission ID : {admission_id}\nPassword : {password}\nRe-Admission Fee : {fee_amount}\nPlease Do Not Share ID & Password.";

                    $this->smsService->triggerEventSms($school, $student->mobile, 'readmission', [
                        '{student_name}' => $student->student_name,
                        '{school_name}' => $school->school_name,
                        '{date}' => $request->readmit_date,
                        '{class_name}' => $className,
                        '{group_name}' => $toGroupName,
                        '{section_name}' => $toSectionName,
                        '{session_year}' => $toSessionYear,
                        '{student_id}' => $student->student_id_number,
                        '{admission_id}' => $newAdmissionId,
                        '{password}' => '00000000',
                        '{fee_amount}' => $request->readmit_fee
                    ], $defaultMessage);
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Students re-admitted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Re-Admission Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to process student re-admission.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch re-admission history log for the current school
     */
    public function getReadmissionHistory(Request $request)
    {
        $schoolUser = Auth::user();
        $school = School::where('user_id', $schoolUser->id)->first();
        if (!$school) {
            return response()->json(['message' => 'School profile not found.'], 400);
        }

        $query = StudentReadmission::with([
            'student:id,student_name,status,inactive_date',
            'schoolClass:id,class_name',
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
                ->orWhere('student_id_number', 'like', "%{$search}%")
                ->orWhere('to_admission_id', 'like', "%{$search}%");
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
                'student_id_number' => $item->student_id_number,
                'from_admission_id' => $item->from_admission_id,
                'to_admission_id' => $item->to_admission_id,
                'class' => $item->schoolClass?->class_name,
                'from_session' => $item->fromSession?->session_year,
                'to_session' => $item->toSession?->session_year,
                'from_group' => $item->fromGroup?->group_name,
                'to_group' => $item->toGroup?->group_name,
                'from_section' => $item->fromSection?->section_name,
                'to_section' => $item->toSection?->section_name,
                'readmission_date' => $item->readmission_date,
                'readmission_fee' => $item->readmission_fee,
            ];
        });

        return response()->json($history);
    }
}
