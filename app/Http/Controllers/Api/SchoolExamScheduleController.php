<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolExamScheduleRequest;
use App\Jobs\PublishScheduledExamResults;
use App\Models\School;
use App\Models\SchoolExamSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolExamScheduleController extends Controller
{
    private function authorizeAccess(): void
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['admin', 'school'], true)) {
            abort(403, 'Unauthorized Access');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAccess();

        $school = School::where('user_id', Auth::id())->first();
        $query = SchoolExamSchedule::where('school_id', $school->id);

        if ($request->filled('class_name')) $query->where('class_name', $request->class_name);
        if ($request->filled('group_name')) $query->where('group_name', $request->group_name);
        if ($request->filled('section_name')) $query->where('section_name', $request->section_name);
        if ($request->filled('session_name')) $query->where('session_name', $request->session_name);
        if ($request->filled('exam_name')) $query->where('exam_name', $request->exam_name);

        $sortBy = $request->input('sort_by', 'id');
        $sortDir = $request->input('sort_dir', 'desc');
        $allowedSorts = ['id', 'class_name', 'group_name', 'section_name', 'session_name', 'exam_name', 'publish_date', 'publish_time', 'status', 'created_at'];

        if (in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('id', 'desc');
        }

        $paginate = $query->paginate(50);
        $paginate->getCollection()->transform(function ($schedule) {
            $schedule->created_by_name = $schedule->created_by ? User::find($schedule->created_by)?->name : null;
            return $schedule;
        });

        return response()->json($paginate);
    }

    public function getCounts(Request $request)
    {
        $this->authorizeAccess();

        $school = School::where('user_id', Auth::id())->first();

        $class = DB::table('school_classes')->where('school_id', $school->id)->where('class_name', $request->class_name)->first();
        $group = $request->filled('group_name') ? DB::table('school_groups')->where('school_id', $school->id)->where('group_name', $request->group_name)->first() : null;
        $section = $request->filled('section_name') ? DB::table('school_sections')->where('school_id', $school->id)->where('section_name', $request->section_name)->first() : null;

        $query = DB::table('school_subjects')->where('school_id', $school->id);
        if ($class) {
            $query->where('class_id', $class->id);
        }
        if ($group) {
            $query->where('group_id', $group->id);
        }
        if ($section) {
            $query->where('section_id', $section->id);
        }

        $totalSubjects = (clone $query)->count();

        $submittedQuery = DB::table('school_exam_marks')
            ->where('school_id', $school->id)
            ->where('class_name', $request->class_name)
            ->where('exam_name', $request->exam_name)
            ->where('session_name', $request->session_name);

        if ($request->filled('group_name')) {
            $submittedQuery->where('group_name', $request->group_name);
        }
        if ($request->filled('section_name')) {
            $submittedQuery->where('section_name', $request->section_name);
        }

        $submittedSubjects = (clone $submittedQuery)->distinct('subject_name')->count('subject_name');

        return response()->json([
            'total' => $totalSubjects,
            'submitted' => $submittedSubjects,
            'remaining' => max(0, $totalSubjects - $submittedSubjects)
        ]);
    }

    public function store(SchoolExamScheduleRequest $request)
    {
        $this->authorizeAccess();

        $school = School::where('user_id', Auth::id())->first();

        $payload = array_merge($request->validated(), [
            'school_id' => $school->id,
            'created_by' => Auth::id(),
            'status' => $request->input('status', 'Active'),
            'published_at' => null,
            'publish_date' => $request->publish_date,
            'publish_time' => $request->publish_time,
        ]);

        $schedule = SchoolExamSchedule::create($payload);

        if ($schedule->status === 'Active' && $schedule->publish_date && $schedule->publish_time) {
            PublishScheduledExamResults::dispatch($schedule->id);
        }

        return response()->json(['message' => 'Created successfully'], 201);
    }

    public function show($id)
    {
        $this->authorizeAccess();

        $school = School::where('user_id', Auth::id())->first();
        return SchoolExamSchedule::where('school_id', $school->id)->findOrFail($id);
    }

    public function update(SchoolExamScheduleRequest $request, $id)
    {
        $this->authorizeAccess();

        $school = School::where('user_id', Auth::id())->first();
        $schedule = SchoolExamSchedule::where('school_id', $school->id)->findOrFail($id);
        $schedule->update(array_merge($request->validated(), [
            'status' => $request->input('status', $schedule->status ?? 'Active'),
            'updated_at' => now(),
        ]));

        if ($schedule->status === 'Active' && $schedule->publish_date && $schedule->publish_time) {
            PublishScheduledExamResults::dispatch($schedule->id);
        }

        return response()->json(['message' => 'Updated successfully']);
    }

    public function destroy($id)
    {
        $this->authorizeAccess();

        $school = School::where('user_id', Auth::id())->first();
        SchoolExamSchedule::where('school_id', $school->id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
