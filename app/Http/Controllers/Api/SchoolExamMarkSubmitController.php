<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolExamMark;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolExamMarkSubmitController extends Controller
{
    public function index(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();
        $query = SchoolExamMark::where('school_id', $school->id);

        if ($request->class_name) $query->where('class_name', $request->class_name);
        if ($request->exam_name) $query->where('exam_name', $request->exam_name);
        if ($request->subject_name) $query->where('subject_name', $request->subject_name);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('student_name', 'LIKE', "%{$request->search}%")
                    ->orWhere('student_id_number', 'LIKE', "%{$request->search}%");
            });
        }

        return response()->json($query->orderBy('id', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();

        return DB::transaction(function () use ($request, $school) {
            foreach ($request->marks_data as $data) {
                // Check if entry already exists for this student in this specific exam/subject
                $exists = SchoolExamMark::where([
                    'school_id' => $school->id,
                    'class_name' => $request->class_name,
                    'session_name' => $request->session_name,
                    'exam_name' => $request->exam_name,
                    'subject_name' => $request->subject_name,
                    'student_id_number' => $data['student_id_number']
                ])->exists();

                if ($exists) {
                    return response()->json([
                        'status' => 'exists',
                        'message' => "Mark already exists for Student ID: {$data['student_id_number']}. Delete the old entry to re-submit."
                    ], 422);
                }

                // Modified on 2026-07-09: Prevent entering marks for inactive students
                $student = \App\Models\AdmissionStudent::where('student_id_number', $data['student_id_number'])->first();
                if ($student && $student->status === 'Inactive') {
                    return response()->json([
                        'status' => 'inactive',
                        'message' => "Student ID: {$data['student_id_number']} is inactive. Cannot submit marks for inactive students."
                    ], 422);
                }

                SchoolExamMark::create([
                    'school_id' => $school->id,
                    'class_name' => $request->class_name,
                    'group_name' => $request->group_name,
                    'section_name' => $request->section_name,
                    'session_name' => $request->session_name,
                    'exam_name' => $request->exam_name,
                    'subject_name' => $request->subject_name,
                    'student_id_number' => $data['student_id_number'],
                    'student_name' => $data['student_name'],
                    'roll_no' => $data['roll_no'] ?? null,
                    'mark' => $data['mark'] ?? 0,
                    'theory_mark' => $data['theory_mark'] ?? 0,
                    'practical_mark' => $data['practical_mark'] ?? 0,
                    'letter_name' => $data['letter_name'] ?? 'F',
                    'point' => $data['point'] ?? 0,
                    'status' => $request->status ?? 'published'
                ]);
            }

            return response()->json(['message' => 'All marks processed successfully!']);
        });
    }

    public function show($id)
    {
        $school = School::where('user_id', Auth::id())->first();
        $mark = SchoolExamMark::where('school_id', $school->id)->findOrFail($id);
        return response()->json($mark);
    }

    public function update(Request $request, $id)
    {
        $school = School::where('user_id', Auth::id())->first();
        $mark = SchoolExamMark::where('school_id', $school->id)->findOrFail($id);

        // Update with the first item in marks_data (since update handles 1 record)
        $data = $request->marks_data[0];

        $mark->update([
            'class_name' => $request->class_name,
            'group_name' => $request->group_name,
            'section_name' => $request->section_name,
            'session_name' => $request->session_name,
            'exam_name' => $request->exam_name,
            'subject_name' => $request->subject_name,
            'mark' => $data['mark'],
            'theory_mark' => $data['theory_mark'] ?? 0,
            'practical_mark' => $data['practical_mark'] ?? 0,
            'letter_name' => $data['letter_name'],
            'point' => $data['point'],
            'status' => $request->status ?? $mark->status
        ]);

        return response()->json(['message' => 'Mark updated successfully']);
    }

    public function destroy($id)
    {
        $school = School::where('user_id', Auth::id())->first();
        SchoolExamMark::where('school_id', $school->id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Record deleted']);
    }
}
