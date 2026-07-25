<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdmissionStudent;
use App\Models\Principal;
use App\Models\School;
use App\Models\SchoolExamAdmitCard;
use App\Models\SchoolExamName;
use App\Models\SchoolExamRoutine;
use App\Models\SchoolFeeTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class SchoolExamAdmitCardController extends Controller
{
    private function getSchool()
    {
        return School::where('user_id', Auth::id())->first();
    }

    private function getSchoolId()
    {
        $school = $this->getSchool();
        return $school ? $school->id : null;
    }

    public function index(Request $request)
    {
        $school = $this->getSchool();
        $schoolId = $school?->id;

        $query = SchoolExamAdmitCard::with([
            'student:student_id_number,student_name,father_name,image'
        ])->where('school_id', $schoolId);

        // Filters
        if ($request->filled('class_name')) {
            $query->where('class_name', $request->class_name);
        }

        if ($request->filled('group_name')) {
            $query->where('group_name', $request->group_name);
        }

        if ($request->filled('section_name')) {
            $query->where('section_name', $request->section_name);
        }

        if ($request->filled('session_name')) {
            $query->where('session_name', $request->session_name);
        }

        if ($request->filled('exam_name')) {
            $query->where('exam_name', $request->exam_name);
        }

        // Search
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';

            $query->where(function ($q) use ($searchTerm) {
                $q->where('student_id_number', 'like', $searchTerm)
                    ->orWhereHas('student', function ($student) use ($searchTerm) {
                        $student->where('student_name', 'like', $searchTerm);
                    });
            });
        }

        $perPage = $request->input('per_page', 10);

        $paginatedData = $query->latest()->paginate($perPage);

        // Add student info to response
        $paginatedData->getCollection()->transform(function ($item) {
            $item->student_name = optional($item->student)->student_name;
            $item->father_name = optional($item->student)->father_name;
            $item->student_image = optional($item->student)->image;

            unset($item->student);

            return $item;
        });

        $response = $paginatedData->toArray();

        // Fetch routines
        $routines = collect();

        if ($schoolId && !empty($response['data'])) {

            $combinations = [];

            foreach ($response['data'] as $card) {
                $key = implode('|', [
                    $card['class_name'],
                    $card['session_name'],
                    $card['exam_name']
                ]);

                $combinations[$key] = [
                    'class_name' => $card['class_name'],
                    'session_name' => $card['session_name'],
                    'exam_name' => $card['exam_name']
                ];
            }

            $routineQuery = SchoolExamRoutine::where('school_id', $schoolId);

            $routineQuery->where(function ($q) use ($combinations) {
                foreach ($combinations as $comb) {
                    $q->orWhere(function ($subQ) use ($comb) {
                        $subQ->where('class_name', $comb['class_name'])
                            ->where('session_name', $comb['session_name'])
                            ->where('exam_name', $comb['exam_name']);
                    });
                }
            });

            $routines = $routineQuery
                ->orderBy('exam_date')
                ->orderBy('start_time')
                ->get();
        }

        $response['routines'] = $routines;

        // School Info
        if ($school) {

            $principal = Principal::where('school_id', $schoolId)->first();

            $response['school_info'] = [
                'school_name' => $school->school_name,
                'village' => $school->village,
                'upazila' => $school->upazila,
                'district' => $school->district,
                'division' => $school->division,
                'mobile' => $school->mobile,
                'email' => $school->email,
                'logo' => $school->logo
                    ? asset('storage/' . $school->logo)
                    : null,
                'principal_signature' => $principal && $principal->signature
                    ? asset('storage/' . $principal->signature)
                    : null,
            ];
        }

        return response()->json($response);
    }

    private function validateAdmitPrerequisites($school_id, $className, $groupName, $sectionName, $sessionName, $examName)
    {
        $class = \App\Models\SchoolClass::where('school_id', $school_id)->where('class_name', $className)->first();
        $session = \App\Models\SchoolSession::where('school_id', $school_id)->where('session_year', $sessionName)->first();
        $group = $groupName ? \App\Models\SchoolGroup::where('school_id', $school_id)->where('group_name', $groupName)->first() : null;
        $section = $sectionName ? \App\Models\SchoolSection::where('school_id', $school_id)->where('section_name', $sectionName)->first() : null;

        $examQuery = SchoolExamName::where('school_id', $school_id)
            ->where('class_id', $class?->id)
            ->where('session_id', $session?->id)
            ->where('exam_name', $examName);

        if ($group) {
            $examQuery->where('group_id', $group->id);
        }

        if ($section) {
            $examQuery->where('section_id', $section->id);
        }

        $examRecord = $examQuery->first();
        if (!$examRecord) {
            return [
                'valid' => false,
                'message' => 'Please create the Exam, Exam Routine, and Exam Fee before generating the Admit Card.'
            ];
        }

        $routineFound = SchoolExamRoutine::where('school_id', $school_id)
            ->where('class_name', $className)
            ->where('session_name', $sessionName)
            ->where('exam_name', $examName)
            ->when(!empty($groupName), fn ($q) => $q->where('group_name', $groupName))
            ->when(!empty($sectionName), fn ($q) => $q->where('section_name', $sectionName))
            ->exists();

        if (!$routineFound) {
            $routineFound = SchoolExamRoutine::where('school_id', $school_id)
                ->where('class_name', $className)
                ->where('session_name', $sessionName)
                ->where('exam_name', $examName)
                ->exists();
        }

        if (!$routineFound) {
            return [
                'valid' => false,
                'message' => 'Please create the Exam, Exam Routine, and Exam Fee before generating the Admit Card.'
            ];
        }

        $feeFound = SchoolFeeTemplate::where('school_id', $school_id)
            ->where('exam_id', $examRecord->id)
            ->where('fee_type_name', 'Exams')
            ->exists();

        if (!$feeFound) {
            return [
                'valid' => false,
                'message' => 'Please create the Exam, Exam Routine, and Exam Fee before generating the Admit Card.'
            ];
        }

        return ['valid' => true];
    }

    public function checkPrerequisites(Request $request)
    {
        $school_id = $this->getSchoolId();

        $validation = $this->validateAdmitPrerequisites(
            $school_id,
            $request->class_name,
            $request->group_name,
            $request->section_name,
            $request->session_name,
            $request->exam_name
        );

        if (!$validation['valid']) {
            return response()->json([
                'valid' => false,
                'message' => $validation['message']
            ]);
        }

        return response()->json(['valid' => true]);
    }

    public function store(Request $request)
    {
        $school_id = $this->getSchoolId();
        $students = $request->students;
        $exam = $request->exam_name;

        $prerequisiteCheck = $this->validateAdmitPrerequisites(
            $school_id,
            $request->class_name,
            $request->group_name,
            $request->section_name,
            $request->session_name,
            $exam
        );

        if (!$prerequisiteCheck['valid']) {
            return response()->json([
                'status' => 'missing_modules',
                'message' => $prerequisiteCheck['message']
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Lock to prevent race condition when multiple schools submit simultaneously
            DB::statement('SELECT GET_LOCK("admit_card_seq", 15)');

            $maxNumber = SchoolExamAdmitCard::max('admit_card_number');
            $currentAdmitNumber = $maxNumber ? (int)$maxNumber + 1 : 24951080;

            foreach ($students as $student) {
                $studentId = $student['student_id_number'];

                $exists = SchoolExamAdmitCard::where([
                    'school_id' => $school_id,
                    'class_name' => $request->class_name,
                    'exam_name' => $exam,
                    'student_id_number' => $studentId,
                ])->exists();

                if ($exists) {
                    DB::statement('SELECT RELEASE_LOCK("admit_card_seq")');
                    DB::rollBack();
                    return response()->json([
                        'status' => 'exists',
                        'message' => "Admit card already exists for Student ID: {$studentId} for this exam."
                    ], 422);
                }

                SchoolExamAdmitCard::create([
                    'school_id' => $school_id,
                    'class_name' => $request->class_name,
                    'group_name' => $request->group_name,
                    'section_name' => $request->section_name,
                    'session_name' => $request->session_name,
                    'exam_name' => $exam,
                    'student_id_number' => $studentId,
                    'admit_card_number' => $currentAdmitNumber,
                ]);

                $currentAdmitNumber++;
            }

            DB::statement('SELECT RELEASE_LOCK("admit_card_seq")');
            DB::commit();
            return response()->json(['message' => 'Admit cards generated successfully.']);
        } catch (\Throwable $e) {
            DB::statement('SELECT RELEASE_LOCK("admit_card_seq")');
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $school_id = $this->getSchoolId();
        $admit = SchoolExamAdmitCard::where('school_id', $school_id)->findOrFail($id);

        $admit->update([
            'exam_name' => $request->exam_name,
            'class_name' => $request->class_name,
            'section_name' => $request->section_name,
            'group_name' => $request->group_name,
            'session_name' => $request->session_name,
            // 'admit_card_number' remains fixed to prevent ID jumping on simple edits
        ]);

        return response()->json(['message' => 'Admit card updated successfully']);
    }

    public function destroy($id)
    {
        $school_id = $this->getSchoolId();
        SchoolExamAdmitCard::where('school_id', $school_id)->where('id', $id)->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function getStudents(Request $request)
    {
        $school_id = $this->getSchoolId();
        $students = AdmissionStudent::where('school_id', $school_id)
            // Exclude Inactive students from admit card generation
            ->where('status', '!=', 'Inactive')
            ->where('class', $request->class_name)
            ->where('session', $request->session_name)
            ->get(['student_id_number', 'student_name']);

        return response()->json(['data' => $students]);
    }
}
