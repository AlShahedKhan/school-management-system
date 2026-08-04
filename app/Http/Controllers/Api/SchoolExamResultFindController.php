<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\TranscriptPdfRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\School;
use App\Models\SchoolExamSchedule;
use App\Models\SchoolSubject;
use App\Models\Principal;
use App\Models\SchoolSession;
use App\Models\SchoolHoliday;
use App\Models\AdmissionStudent;
use App\Models\Attendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class SchoolExamResultFindController extends Controller
{
    public function exportPdf(Request $request, TranscriptPdfRenderer $pdfRenderer)
    {
        $request->merge(['mode' => 'single']);

        $result = $this->findResult($request);

        if (! $result instanceof JsonResponse || $result->getStatusCode() >= 400) {
            return $result;
        }

        $resultData = $result->getData(true);
        $token = Str::random(64);
        $cacheKey = 'transcript-pdf:'.$token;

        Cache::put($cacheKey, [
            'user_id' => Auth::id(),
            'result' => $resultData,
        ], now()->addMinutes(2));

        $previewUrl = URL::temporarySignedRoute(
            'internal.school.result-pdf-preview',
            now()->addMinutes(2),
            ['token' => $token]
        );

        try {
            $pdf = $pdfRenderer->render($previewUrl);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'The transcript PDF could not be generated. Please try again.',
            ], 500);
        } finally {
            Cache::forget($cacheKey);
        }

        $filename = 'academic-result-'.trim((string) $request->input('admit_no')).'.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Length' => (string) strlen($pdf),
        ]);
    }

    public function findResult(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();

        if (!$school) {
            return response()->json(['message' => 'School context not found'], 404);
        }

        if ($request->input('mode') !== 'single') {
            return response()->json([
                'message' => 'Results can only be searched with an Admit Card Number.',
            ], 422);
        }

        // --- MODE: SINGLE RESULT (Transcript) ---
        if ($request->mode === 'single') {
            $request->validate([
                'admit_no'   => 'required',
            ]);

            $admitCard = DB::table('school_exam_admit_cards as ac')
                ->join('admission_students as s', 'ac.student_id_number', '=', 's.student_id_number')
                ->where('ac.school_id', $school->id)
                ->where('ac.admit_card_number', $request->admit_no)
                ->select(
                    's.student_name',
                    's.father_name',
                    's.mother_name',
                    's.image',
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
                return response()->json(['message' => 'Invalid Admit Card Number'], 422);
            }

            $studentId = $admitCard->student_id_number;

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
                ? \Carbon\Carbon::parse($scheduled->publish_date . ' ' . $scheduled->publish_time)->format('d-F-y h:i A')
                : null;

            $marks = DB::table('school_exam_marks')
                ->where('school_id', $school->id)
                ->where('student_id_number', $studentId)
                ->where('exam_name', $admitCard->exam_name)
                ->get();

            if ($marks->isEmpty()) {
                return response()->json(['message' => 'No marks found for this exam record'], 404);
            }

            $subjectDefinitions = SchoolSubject::query()
                ->where('school_id', $school->id)
                ->whereIn('subject_name', $marks->pluck('subject_name')->filter()->unique())
                ->whereHas('school_class', function ($query) use ($admitCard) {
                    $query->where('class_name', $admitCard->class_name);
                })
                ->when($admitCard->group_name, function ($query) use ($admitCard) {
                    $query->whereHas('school_group', function ($groupQuery) use ($admitCard) {
                        $groupQuery->where('group_name', $admitCard->group_name);
                    });
                })
                ->when($admitCard->section_name, function ($query) use ($admitCard) {
                    $query->whereHas('school_section', function ($sectionQuery) use ($admitCard) {
                        $sectionQuery->where('section_name', $admitCard->section_name);
                    });
                })
                ->with('grade_type:id,full_mark')
                ->get()
                ->keyBy('subject_name');

            $highestMarks = DB::table('school_exam_marks')
                ->where('school_id', $school->id)
                ->where('exam_name', $admitCard->exam_name)
                ->where('class_name', $admitCard->class_name)
                ->where('session_name', $admitCard->session_name)
                ->when($admitCard->group_name, function ($query) use ($admitCard) {
                    $query->where('group_name', $admitCard->group_name);
                })
                ->when($admitCard->section_name, function ($query) use ($admitCard) {
                    $query->where('section_name', $admitCard->section_name);
                })
                ->select('subject_name', DB::raw('MAX(mark) as highest_mark'))
                ->groupBy('subject_name')
                ->pluck('highest_mark', 'subject_name');

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

                if ($row->student_id_number === $studentId) {
                    $position = $rank;
                    break;
                }

                $previousTotal = $row->total_marks;
                $previousRank = $rank;
            }

            $subjectDetails = $marks->map(function ($m) use ($gradingScale, $highestMarks, $subjectDefinitions) {
                $subjectDefinition = $subjectDefinitions->get($m->subject_name);
                $configuredMarks = $subjectDefinition?->marks ?? [];
                $fullMark = (int) (
                    $subjectDefinition?->grade_type?->full_mark
                    ?? ($configuredMarks['total_mark'] ?? null)
                    ?? ($m->mark > 50 ? 100 : ($m->mark > 10 ? 50 : 10))
                );
                $gradeName = $m->letter_name;
                $gradePoint = $m->point;
                $tutorialMark = $m->tutorial_mark ?? 0;
                $mcqMark = $m->mcq_mark ?? 0;
                $writingMark = $m->writing_mark ?? $m->theory_mark ?? 0;
                $practicalMark = $m->practical_mark ?? 0;

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
                    'highest_mark'  => $highestMarks->get($m->subject_name, $m->mark),
                    'mark'          => $m->mark,
                    'fail_mark'     => is_numeric($subjectDefinition?->fail_mark)
                        ? (float) $subjectDefinition->fail_mark
                        : (float) ($subjectDefinition?->grade_type?->mark_from ?? 0),
                    'tutorial_mark' => $tutorialMark,
                    'mcq_mark'      => $mcqMark,
                    'writing_mark'  => $writingMark,
                    'theory_mark'   => $m->theory_mark ?? 0,
                    'practical_mark'=> $practicalMark,
                    'grade'         => $gradeName ?? '-',
                    'point'         => $gradePoint,
                ];
            });

            $avgPoint = $subjectDetails->avg('point');
            $finalGrade = $this->calculateFinalGrade($avgPoint, $gradingScale);
            $attendanceSummary = $this->attendanceSummary($school, $admitCard, $studentId);
            $verificationUrl = URL::temporarySignedRoute(
                'public.result.verify',
                now()->addYear(),
                [
                    'school' => $school->id,
                    'student' => $studentId,
                    'admit_no' => $admitCard->admit_card_number,
                    'exam' => $admitCard->exam_name,
                    'session' => $admitCard->session_name,
                ]
            );
            $qrCode = (new SvgWriter())->write(new QrCode($verificationUrl))->getDataUri();

            return response()->json([
                'student_name'      => $admitCard->student_name,
                'student_image'    => $admitCard->image ? asset('storage/' . $admitCard->image) : null,
                'father_name'       => $admitCard->father_name,
                'mother_name'      => $admitCard->mother_name,
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
                'gpa_without_fourth' => number_format($avgPoint, 2),
                'grade'             => $finalGrade,
                'position'          => $position,
                'position_total'    => $positionList->count(),
                'attendance'        => $attendanceSummary,
                'verification_url'  => $verificationUrl,
                'qr_code'           => $qrCode,
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

    private function attendanceSummary(School $school, object $admitCard, string $studentId): array
    {
        $session = SchoolSession::query()
            ->where('school_id', $school->id)
            ->where('session_year', $admitCard->session_name)
            ->whereHas('schoolClass', fn ($query) => $query->where('class_name', $admitCard->class_name))
            ->when($admitCard->group_name, fn ($query) => $query->whereHas(
                'schoolGroup',
                fn ($groupQuery) => $groupQuery->where('group_name', $admitCard->group_name)
            ))
            ->when($admitCard->section_name, fn ($query) => $query->whereHas(
                'schoolSection',
                fn ($sectionQuery) => $sectionQuery->where('section_name', $admitCard->section_name)
            ))
            ->first();

        if (! $session || ! $session->start_date || ! $session->end_date) {
            return [
                'present_days' => 0,
                'absent_days' => 0,
                'working_days' => 0,
                'percentage' => 0,
            ];
        }

        $start = Carbon::parse($session->start_date)->startOfDay();
        $end = Carbon::parse($session->end_date)->startOfDay();
        $sessionDates = collect(CarbonPeriod::create($start, $end));
        $holidayDates = collect();

        SchoolHoliday::query()
            ->where('school_id', $school->id)
            ->whereDate('end_date', '>=', $start->toDateString())
            ->whereDate('start_date', '<=', $end->toDateString())
            ->where(function ($query) use ($admitCard) {
                $query->where('type', 'General')
                    ->orWhere(function ($classWiseQuery) use ($admitCard) {
                        $classWiseQuery->where('type', 'Class Wise')
                            ->where('class_name', $admitCard->class_name)
                            ->when($admitCard->group_name, fn ($q) => $q->where('group_name', $admitCard->group_name))
                            ->when($admitCard->section_name, fn ($q) => $q->where('section_name', $admitCard->section_name))
                            ->where(function ($sessionQuery) use ($admitCard) {
                                $sessionQuery->whereNull('session')
                                    ->orWhere('session', $admitCard->session_name);
                            });
                    });
            })
            ->get(['start_date', 'end_date'])
            ->each(function ($holiday) use ($start, $end, $holidayDates): void {
                $holidayStart = Carbon::parse($holiday->start_date)->startOfDay()->max($start);
                $holidayEnd = Carbon::parse($holiday->end_date)->startOfDay()->min($end);

                if ($holidayStart->lte($holidayEnd)) {
                    foreach (CarbonPeriod::create($holidayStart, $holidayEnd) as $date) {
                        $holidayDates->push($date->toDateString());
                    }
                }
            });

        $workingDays = $sessionDates
            ->map(fn (Carbon $date) => $date->toDateString())
            ->diff($holidayDates->unique())
            ->count();

        $student = AdmissionStudent::query()
            ->where('school_id', $school->user_id)
            ->where('student_id_number', $studentId)
            ->first();

        $presentDays = $student
            ? Attendance::query()
                ->where('attendable_type', AdmissionStudent::class)
                ->where('attendable_id', $student->id)
                ->whereBetween('timestamp', [$start, $end->copy()->endOfDay()])
                ->whereRaw('LOWER(status) = ?', ['present'])
                ->get(['timestamp'])
                ->map(fn ($attendance) => Carbon::parse($attendance->timestamp)->toDateString())
                ->unique()
                ->count()
            : 0;

        $absentDays = max(0, $workingDays - $presentDays);

        return [
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'working_days' => $workingDays,
            'percentage' => $workingDays > 0
                ? round(($presentDays / $workingDays) * 100, 1)
                : 0,
        ];
    }

    private function calculateFinalGrade($gpa, $grades = null)
    {
        $gpa = (float) $gpa;

        if (!$grades || count($grades) === 0) {
            if ($gpa >= 5.0) return 'A+';
            if ($gpa >= 4.0) return 'A';
            if ($gpa >= 3.5) return 'A-';
            if ($gpa >= 3.0) return 'B';
            if ($gpa >= 2.0) return 'C';
            if ($gpa >= 1.0) return 'D';
            return 'F';
        }

        $gpaGrades = collect($grades)
            ->filter(function ($grade) {
                return is_numeric($grade->grade_point)
                    && (float) $grade->grade_point >= 0
                    && (float) $grade->grade_point <= 5;
            })
            ->groupBy(function ($grade) {
                return (float) $grade->full_mark;
            })
            ->sortKeysDesc()
            ->first()
            ?->sortByDesc(function ($grade) {
                return (float) $grade->grade_point;
            }) ?? collect();

        foreach ($gpaGrades as $grade) {
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
