<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\School;
use App\Models\SchoolExamSchedule;
use App\Models\Principal;
use Illuminate\Support\Facades\Auth;

class SchoolExamResultFindController extends Controller
{
    public function findResult(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();

        if (!$school) {
            return response()->json(['message' => 'School context not found'], 404);
        }

        // --- MODE: SINGLE RESULT (Transcript) ---
        if ($request->mode === 'single') {
            $request->validate([
                'student_id' => 'required',
                'admit_no'   => 'required',
            ]);

            $admitCard = DB::table('school_exam_admit_cards as ac')
                ->join('admission_students as s', 'ac.student_id_number', '=', 's.student_id_number')
                ->where('ac.school_id', $school->id)
                ->where('ac.student_id_number', $request->student_id)
                ->where('ac.admit_card_number', $request->admit_no)
                ->select(
                    's.student_name',
                    's.father_name',
                    'ac.student_id_number',
                    'ac.class_name',
                    'ac.group_name',
                    'ac.section_name',
                    'ac.exam_name',
                    'ac.session_name',
                    'ac.admit_card_number'
                )
                ->first();

            if (!$admitCard) {
                return response()->json(['message' => 'Invalid Student ID or Admit Card Number'], 422);
            }

            // Check publication based on admit card details
            $scheduled = SchoolExamSchedule::where('school_id', $school->id)
                ->where('exam_name', $admitCard->exam_name)
                ->where('class_name', $admitCard->class_name)
                ->where('session_name', $admitCard->session_name)
                ->when($admitCard->section_name, function ($q) use ($admitCard) {
                    return $q->where('section_name', $admitCard->section_name);
                })
                ->when($admitCard->group_name, function ($q) use ($admitCard) {
                    return $q->where('group_name', $admitCard->group_name);
                })
                ->orderByDesc('id')
                ->first();

            if (!$scheduled) {
                return response()->json(['status' => 'warning', 'message' => 'Result has not been published yet.'], 403);
            }

            $publishAt = \Carbon\Carbon::parse($scheduled->publish_date . ' ' . $scheduled->publish_time);
            if ($scheduled->status !== 'Published') {
                if ($scheduled->status === 'Active' && now()->gte($publishAt)) {
                    $scheduled->update(['status' => 'Published', 'published_at' => now()]);
                } else {
                    return response()->json(['status' => 'warning', 'message' => 'Result has not been published yet.'], 403);
                }
            }

            $publishDateTime = $scheduled->publish_date && $scheduled->publish_time
                ? \Carbon\Carbon::parse($scheduled->publish_date . ' ' . $scheduled->publish_time)->format('j-F-Y h:i A')
                : null;

            $marks = DB::table('school_exam_marks')
                ->where('school_id', $school->id)
                ->where('student_id_number', $request->student_id)
                ->where('exam_name', $admitCard->exam_name)
                ->get();

            if ($marks->isEmpty()) {
                return response()->json(['message' => 'No marks found for this exam record'], 404);
            }

            $gradingScale = DB::table('school_exam_grades')
                ->where('school_id', $school->id)
                ->select('full_mark', 'mark_from', 'mark_to', 'grade_name', 'grade_point')
                ->orderBy('full_mark', 'asc')
                ->orderBy('grade_point', 'desc')
                ->get();

            $principal = Principal::where('school_id', $school->id)->latest()->first();

            $positionList = DB::table('school_exam_marks')
                ->where('school_id', $school->id)
                ->where('exam_name', $admitCard->exam_name)
                ->where('class_name', $admitCard->class_name)
                ->where('session_name', $admitCard->session_name)
                ->when($admitCard->section_name, function ($q) use ($admitCard) {
                    return $q->where('section_name', $admitCard->section_name);
                })
                ->when($admitCard->group_name, function ($q) use ($admitCard) {
                    return $q->where('group_name', $admitCard->group_name);
                })
                ->select('student_id_number', DB::raw('SUM(mark) as total_marks'))
                ->groupBy('student_id_number')
                ->orderByDesc('total_marks')
                ->get();

            $position = 'N/A';
            $previousTotal = null;
            $previousRank = 0;

            foreach ($positionList as $index => $row) {
                $rank = $index + 1;
                if ($previousTotal !== null && $row->total_marks == $previousTotal) {
                    $rank = $previousRank;
                }

                if ($row->student_id_number === $request->student_id) {
                    $position = $rank;
                    break;
                }

                $previousTotal = $row->total_marks;
                $previousRank = $rank;
            }

            $subjectDetails = $marks->map(function ($m) use ($gradingScale) {
                $fullMark = $m->mark > 50 ? 100 : ($m->mark > 10 ? 50 : 10);
                $gradeName = $m->letter_name;
                $gradePoint = $m->point;

                $matchingRules = $gradingScale->filter(function ($rule) use ($fullMark) {
                    return (int) $rule->full_mark === (int) $fullMark;
                })->values();

                if ($matchingRules->isNotEmpty()) {
                    $matchedRule = $matchingRules->first(function ($rule) use ($m) {
                        return $m->mark >= $rule->mark_from && $m->mark <= $rule->mark_to;
                    });

                    if ($matchedRule) {
                        $gradeName = $matchedRule->grade_name;
                        $gradePoint = $matchedRule->grade_point;
                    }
                }

                return [
                    'name'          => $m->subject_name,
                    'full_mark'     => $fullMark,
                    'mark'          => $m->mark,
                    'theory_mark'   => $m->theory_mark ?? 0,
                    'practical_mark'=> $m->practical_mark ?? 0,
                    'grade'         => $gradeName ?? '-',
                    'point'         => $gradePoint,
                ];
            });

            $avgPoint = $subjectDetails->avg('point');
            $finalGrade = $this->calculateFinalGrade($avgPoint, $gradingScale);

            return response()->json([
                'student_name'      => $admitCard->student_name,
                'father_name'       => $admitCard->father_name,
                'student_id_number' => $admitCard->student_id_number,
                'class_name'        => $admitCard->class_name,
                'group_name'        => $admitCard->group_name,
                'section_name'      => $admitCard->section_name,
                'admit_card_number' => $admitCard->admit_card_number,
                'roll_no'           => $marks->first()->roll_no ?? 'N/A',
                'exam_name'         => $admitCard->exam_name,
                'session_name'      => $admitCard->session_name,
                'school_info' => [
                    'school_name' => $school->school_name,
                    'village'     => $school->village,
                    'upazila'     => $school->upazila,
                    'district'    => $school->district,
                    'division'    => $school->division,
                    'mobile'      => $school->mobile,
                    'email'       => $school->email,
                    'logo'        => $school->logo ? asset('storage/' . $school->logo) : null,
                    'principal_signature' => $principal && $principal->signature ? asset('storage/' . $principal->signature) : null,
                ],
                'total_marks'       => $marks->sum('mark'),
                'gpa'               => number_format($avgPoint, 2),
                'grade'             => $finalGrade,
                'position'          => $position,
                'subjects'          => $subjectDetails,
                'grading_scale'     => $gradingScale,
                'publish_datetime'  => $publishDateTime,
            ]);
        }

        // --- MODE: CLASSWISE RESULT ---
        else {
           
            $request->validate([
                'class'   => 'required',
                'exam'    => 'required',
                'session' => 'required',
                'group' => 'required',
                'section' => 'required',
            ]);

            // Check publication for batch result
            $scheduled = SchoolExamSchedule::where('school_id', $school->id)
                ->where('exam_name', $request->exam)
                ->where('class_name', $request->class)
                ->where('session_name', $request->session)
                ->when($request->section, function ($q) use ($request) {
                    return $q->where('section_name', $request->section);
                })
                ->when($request->group, function ($q) use ($request) {
                    return $q->where('group_name', $request->group);
                })
                ->orderByDesc('id')
                ->first();

            if (!$scheduled) {
                return response()->json(['status' => 'warning', 'message' => 'Result has not been published yet.'], 403);
            }

            $publishAt = \Carbon\Carbon::parse($scheduled->publish_date . ' ' . $scheduled->publish_time);
            if ($scheduled->status !== 'Published') {
                if ($scheduled->status === 'Active' && now()->gte($publishAt)) {
                    $scheduled->update(['status' => 'Published', 'published_at' => now()]);
                } else {
                    return response()->json(['status' => 'warning', 'message' => 'Result has not been published yet.'], 403);
                }
            }

            $allMarks = DB::table('school_exam_marks')
                ->where('school_id', $school->id)
                ->where('class_name', $request->class)
                ->where('exam_name', $request->exam)
                ->where('session_name', $request->session)
                ->when($request->section, function ($q) use ($request) {
                    return $q->where('section_name', $request->section);
                })
                ->when($request->group, function ($q) use ($request) {
                    return $q->where('group_name', $request->group);
                })
                ->get();

            if ($allMarks->isEmpty()) {
                return response()->json(['message' => 'No results found for this selection'], 404);
            }

            $subjectsList = $allMarks->pluck('subject_name')->unique()->values();

            $gradingScale = DB::table('school_exam_grades')
                ->where('school_id', $school->id)
                ->select('full_mark', 'mark_from', 'mark_to', 'grade_name', 'grade_point')
                ->orderBy('full_mark', 'asc')
                ->orderBy('grade_point', 'desc')
                ->get();

            $studentsData = $allMarks->groupBy('student_id_number')->map(function ($marks, $studentId) use ($gradingScale) {
                $first = $marks->first();
                $subjectMarks = [];
                foreach ($marks as $m) {
                    $subjectMarks[$m->subject_name] = $m->mark;
                }
                $avgPoint = $marks->avg('point');

                return [
                    'id'         => $first->id,
                    'student_id' => $studentId,
                    'name'       => $first->student_name ?? 'N/A',
                    'marks'      => $subjectMarks,
                    'total'      => $marks->sum('mark'),
                    'gpa'        => number_format($avgPoint, 2),
                    'grade'      => $this->calculateFinalGrade($avgPoint, $gradingScale)
                ];
            })->values();

            return response()->json([
                'school_name'   => $school->school_name,
                'location'      => $school->upazila . ', ' . $school->district,
                'school_logo'   => $school->logo ? asset('storage/' . $school->logo) : null,
                'subjects_list' => $subjectsList,
                'students'      => $studentsData,
                'grading_scale' => $gradingScale
            ]);
        }
    }

    private function calculateFinalGrade($gpa, $grades = null)
    {
        if (!$grades || count($grades) == 0) {
            if ($gpa >= 5.0) return 'A+';
            if ($gpa >= 4.0) return 'A';
            if ($gpa >= 3.5) return 'A-';
            if ($gpa >= 3.0) return 'B';
            if ($gpa >= 2.0) return 'C';
            if ($gpa >= 1.0) return 'D';
            return 'F';
        }

        foreach ($grades as $grade) {
            if ($gpa >= $grade->grade_point) {
                return $grade->grade_name;
            }
        }
        return 'F';
    }

    public function destroy($id)
    {
        DB::table('school_exam_marks')->where('id', $id)->delete();
        return response()->json(['message' => 'Record deleted successfully']);
    } 
}