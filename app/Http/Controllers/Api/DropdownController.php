<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolExamName;
use App\Models\SchoolGroup;
use App\Models\SchoolSection;
use App\Models\SchoolSession;
use App\Models\SchoolSubject;
use App\Models\SchoolExamAdmitCard;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DropdownController extends Controller
{
    /**
     * Helper to get the school instance for the logged-in user.
     */
    private function getSchool()
    {

        $user = Auth::user();

        if ($user && $user->role === 'teacher') {

            $teacher = Teacher::where('id_number', $user->id_number)->first();

            if ($teacher) {

                return School::find($teacher->school_id);

            }

        }

        return School::where('user_id', Auth::id())->first();

    }

    private function getSchoolScopeIds($school): array
    {
        return collect([
            $school?->id,
            $school?->user_id,
            Auth::id(),
        ])->filter(fn ($value) => $value !== null && $value !== '')
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values()
            ->all();
    }

    public function getSchoolInfo()
    {
        $school = $this->getSchool();
        if (!$school) return response()->json(['message' => 'School not found'], 404);

        return response()->json(['data' => $school]);
    }

    public function getClasses()
    {
        $school = $this->getSchool();
        if (!$school) return response()->json(['data' => []], 404);

        $query = SchoolClass::where('school_id', $school->id);

        // Added on 2026-07-11: Filter classes by teacher permissions if the user is a teacher
        $user = Auth::user();
        if ($user && $user->role === 'teacher') {
            $teacher = \App\Models\Teacher::where('id_number', $user->id_number)->first();
            if ($teacher) {
                $allowedClassIds = \App\Models\TeacherClassPermission::where('teacher_id', $teacher->id)
                    ->pluck('class_id')
                    ->unique()
                    ->all();
                $query->whereIn('id', $allowedClassIds);
            }
        }

        $data = $query->get();
        return response()->json(['data' => $data]);
    }

    public function getGroups(Request $request)
    {
        $school = $this->getSchool();
        $query = SchoolGroup::where('school_id', $school->id);

        $classIds = $request->input('class_ids', []);
        if (empty($classIds) && $request->filled('class_id')) {
            $classIds = [$request->class_id];
        }

        if (!empty($classIds)) {
            $query->whereIn('class_id', Arr::wrap($classIds));
        }

        // Added on 2026-07-11: Filter groups by teacher permissions if the user is a teacher
        $user = Auth::user();
        if ($user && $user->role === 'teacher') {
            $teacher = \App\Models\Teacher::where('id_number', $user->id_number)->first();
            if ($teacher) {
                $allowedGroupIds = \App\Models\TeacherClassPermission::where('teacher_id', $teacher->id)
                    ->pluck('group_id')
                    ->unique()
                    ->filter()
                    ->all();
                $query->whereIn('id', $allowedGroupIds);
            }
        }

        return response()->json(['data' => $query->get()]);
    }

    public function getSections(Request $request)
    {
        $school = $this->getSchool();
        $query = SchoolSection::where('school_id', $school->id);

        $classIds = $request->input('class_ids', []);
        if (empty($classIds) && $request->filled('class_id')) {
            $classIds = [$request->class_id];
        }
        if (!empty($classIds)) {
            $query->whereIn('class_id', Arr::wrap($classIds));
        }

        $groupIds = $request->input('group_ids', []);
        if (empty($groupIds) && $request->filled('group_id')) {
            $groupIds = [$request->group_id];
        }
        if (!empty($groupIds)) {
            $query->whereIn('group_id', Arr::wrap($groupIds));
        }

        // Added on 2026-07-11: Filter sections by teacher permissions if the user is a teacher
        $user = Auth::user();
        if ($user && $user->role === 'teacher') {
            $teacher = \App\Models\Teacher::where('id_number', $user->id_number)->first();
            if ($teacher) {
                $allowedSectionIds = \App\Models\TeacherClassPermission::where('teacher_id', $teacher->id)
                    ->pluck('section_id')
                    ->unique()
                    ->filter()
                    ->all();
                $query->whereIn('id', $allowedSectionIds);
            }
        }

        return response()->json(['data' => $query->get()]);
    }

    public function getSessions(Request $request)
    {
        $school = $this->getSchool();
        if (!$school) return response()->json(['data' => []], 404);

        $query = SchoolSession::where('school_id', $school->id)->active();

        $classIds = $request->input('class_ids', []);
        if (empty($classIds) && $request->filled('class_id')) {
            $classIds = [$request->class_id];
        }

        if (!empty($classIds)) {
            $query->whereIn('class_id', Arr::wrap($classIds));
        } else {
            return response()->json(['data' => []]);
        }

        $groupIds = $request->input('group_ids', []);
        if (empty($groupIds) && $request->filled('group_id')) {
            $groupIds = [$request->group_id];
        }
        if (!empty($groupIds)) {
            $query->whereIn('group_id', Arr::wrap($groupIds));
        }

        $sectionIds = $request->input('section_ids', []);
        if (empty($sectionIds) && $request->filled('section_id')) {
            $sectionIds = [$request->section_id];
        }
        if (!empty($sectionIds)) {
            $query->whereIn('section_id', Arr::wrap($sectionIds));
        }

        return response()->json(['data' => $query->get()]);
    }

    public function getSessionsWithInactiveData(Request $request)
    {
        $school = $this->getSchool();
        if (!$school) return response()->json(['data' => []], 404);

        $query = SchoolSession::where('school_id', $school->id);

        $classIds = $request->input('class_ids', []);
        if (empty($classIds) && $request->filled('class_id')) {
            $classIds = [$request->class_id];
        }

        if (!empty($classIds)) {
            $query->whereIn('class_id', Arr::wrap($classIds));
        } else {
            return response()->json(['data' => []]);
        }

        $groupIds = $request->input('group_ids', []);
        if (empty($groupIds) && $request->filled('group_id')) {
            $groupIds = [$request->group_id];
        }
        if (!empty($groupIds)) {
            $query->whereIn('group_id', Arr::wrap($groupIds));
        }

        $sectionIds = $request->input('section_ids', []);
        if (empty($sectionIds) && $request->filled('section_id')) {
            $sectionIds = [$request->section_id];
        }
        if (!empty($sectionIds)) {
            $query->whereIn('section_id', Arr::wrap($sectionIds));
        }

        return response()->json(['data' => $query->get()]);
    }

    /**
     * Get Subjects filtered by class/group/section
     */
    public function getSubjects(Request $request)
    {
        $school = $this->getSchool();
        $query = SchoolSubject::where('school_id', $school->id);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        // Added on 2026-07-11: Filter subjects by teacher permissions if the user is a teacher
        $user = Auth::user();
        if ($user && $user->role === 'teacher') {
            $teacher = \App\Models\Teacher::where('id_number', $user->id_number)->first();
            if ($teacher) {
                $allowedSubjectIds = \App\Models\TeacherClassPermission::where('teacher_id', $teacher->id)
                    ->pluck('subject_id')
                    ->unique()
                    ->filter()
                    ->all();
                $query->whereIn('id', $allowedSubjectIds);
            }
        }

        return response()->json(['data' => $query->get()]);
    }

    /**
     * Get Exam Names filtered by class, group, section, and session
     */
    public function getExams(Request $request)
    {
        $school = $this->getSchool();

        $query = SchoolExamName::where('school_id', $school->id)
            ->with(['schoolClass', 'schoolGroup', 'schoolSection', 'schoolSession']);

        $classIds = $request->input('class_ids', $request->input('class_id'));
        if (!empty($classIds)) {
            $query->whereIn('class_id', Arr::wrap($classIds));
        }
        $groupIds = $request->input('group_ids', $request->input('group_id'));
        if (!empty($groupIds)) {
            $query->whereIn('group_id', Arr::wrap($groupIds));
        }
        $sectionIds = $request->input('section_ids', $request->input('section_id'));
        if (!empty($sectionIds)) {
            $query->whereIn('section_id', Arr::wrap($sectionIds));
        }
        $sessionIds = $request->input('session_ids', $request->input('session_id'));
        if (!empty($sessionIds)) {
            $query->whereIn('session_id', Arr::wrap($sessionIds));
        }

        $exams = $query->get();

        return response()->json([
            'data' => $exams
        ]);
    }

    public function getStudents(Request $request)
    {
        try {
            $school = $this->getSchool();
            if (!$school) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // 1. Validation: Check if admit card is generated
            if (isset($request->action_type) && $request->action_type == 'exam configure') {
                $className = SchoolClass::where('id', $request->class_id)->value('class_name');

                $admitExists = SchoolExamAdmitCard::where('school_id', $school->id)
                    ->where('class_name', $className)
                    ->exists();

                if (!$admitExists) {
                    return response()->json([
                        'status' => 'no_admit_card',
                        'message' => 'Please generate admit cards first.'
                    ], 200);
                }
            }

            $schoolScopeIds = $this->getSchoolScopeIds($school);
            $query = DB::table('admission_students')
                // Exclude Inactive students from operational dropdown selection
                ->where('status', '!=', 'Inactive');

            if (!empty($schoolScopeIds)) {
                $query->where(function ($q) use ($schoolScopeIds) {
                    foreach ($schoolScopeIds as $schoolId) {
                        $q->orWhere('school_id', $schoolId);
                    }
                });
            }

            $classIds = $request->input('class_ids', []);
            if (empty($classIds) && $request->filled('class_id')) {
                $classIds = [$request->class_id];
            }
            if (!empty($classIds)) {
                $query->whereIn('class', Arr::wrap($classIds));
            }

            $sessionIds = $request->input('session_ids', []);
            if (empty($sessionIds) && $request->filled('session_id')) {
                $sessionIds = [$request->session_id];
            }
            if (!empty($sessionIds)) {
                $query->whereIn('session', Arr::wrap($sessionIds));
            }

            $groupIds = $request->input('group_ids', []);
            if (empty($groupIds) && $request->filled('group_id')) {
                $groupIds = [$request->group_id];
            }
            if (!empty($groupIds)) {
                $query->whereIn('group', Arr::wrap($groupIds));
            }

            $sectionIds = $request->input('section_ids', []);
            if (empty($sectionIds) && $request->filled('section_id')) {
                $sectionIds = [$request->section_id];
            }
            if (!empty($sectionIds)) {
                $query->whereIn('section', Arr::wrap($sectionIds));
            }

            $students = $query->select('id', 'student_name', 'student_id_number')
                ->orderBy('student_name', 'asc')
                ->get();

            return response()->json([
                'data' => $students
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkAdmitCardStatus(Request $request)
    {
        try {
            $school = $this->getSchool();
            if (!$school) {
                return response()->json(['message' => 'School not found'], 404);
            }

            // Resolve names from IDs if names are not provided
            $className = $request->class_name ?: ($request->filled('class_id') ? SchoolClass::where('id', $request->class_id)->value('class_name') : null);
            $sessionName = $request->session_name ?: ($request->filled('session_id') ? SchoolSession::where('id', $request->session_id)->value('session_year') : null);
            $groupName = $request->group_name ?: ($request->filled('group_id') ? SchoolGroup::where('id', $request->group_id)->value('group_name') : null);
            $sectionName = $request->section_name ?: ($request->filled('section_id') ? SchoolSection::where('id', $request->section_id)->value('section_name') : null);

            $query = SchoolExamAdmitCard::where('school_id', $school->id)
                ->where('class_name', $className)
                ->where('session_name', $sessionName);

            if ($groupName) {
                $query->where('group_name', $groupName);
            }
            if ($sectionName) {
                $query->where('section_name', $sectionName);
            }
            if ($request->filled('exam_name')) {
                $query->where('exam_name', $request->exam_name);
            }

            $exists = $query->exists();

            return response()->json([
                'exists' => $exists,
                'message' => $exists ? 'Admit cards found.' : 'No admit cards found. Please generate admit cards first.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to check admit card status.'
            ], 500);
        }
    }
}
