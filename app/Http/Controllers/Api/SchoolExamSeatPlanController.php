<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolExamSeatPlan;
use App\Models\SchoolGroup;
use App\Models\SchoolSection;
use App\Models\SchoolSession;
use App\Services\SeatPlanGenerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolExamSeatPlanController extends Controller
{
    /**
     * Get the school record for the authenticated user.
     */
    private function getSchool()
    {
        return School::where('user_id', Auth::id())->first();
    }

    private function getSchoolScopeIds($school): array
    {
        return collect([
            $school?->id,
            $school?->user_id,
            Auth::id(),
        ])->filter(fn($value) => $value !== null && $value !== '')
            ->map(fn($value) => (int) $value)
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeSelection($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value)));
        }

        if (is_string($value)) {
            $value = trim($value);
            return $value !== '' ? [$value] : [];
        }

        return [];
    }

    private function normalizeIds($value): array
    {
        $values = is_array($value) ? $value : [$value];

        return array_values(array_filter(array_map(function ($item) {
            return is_numeric($item) ? (int) $item : null;
        }, $values)));
    }

    private function resolveSelectedIds(Request $request, string $idKey, string $nameKey, string $modelClass, string $nameColumn, int $schoolId): array
    {
        $ids = $this->normalizeIds($request->input($idKey));
        if (!empty($ids)) {
            return $ids;
        }

        $names = $this->normalizeSelection($request->input($nameKey));
        if (empty($names)) {
            $fallbackNameKey = str_replace('_names', '', $nameKey);
            $names = $this->normalizeSelection($request->input($fallbackNameKey));
        }

        if (empty($names)) {
            return [];
        }

        return $modelClass::where('school_id', $schoolId)
            ->whereIn($nameColumn, $names)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();
    }

    private function isEligibleStudent($student): bool
    {
        $status = strtolower((string) ($student->status ?? ''));
        $ineligible = ['inactive', 'tc', 'transfer certificate', 'transfer_certificate', 'dropout', 'deleted'];

        return !in_array($status, $ineligible, true);
    }

    private function filteredQuery(Request $request, School $school)
    {
        return SchoolExamSeatPlan::with([
            'student:id,student_id_number,student_name'
        ])
            ->where('school_id', $school->id)
            ->when($request->filled('class_name'), fn ($q) => $q->where('class_name', $request->class_name))
            ->when($request->filled('group_name'), fn ($q) => $q->where('group_name', $request->group_name))
            ->when($request->filled('section_name'), fn ($q) => $q->where('section_name', $request->section_name))
            ->when($request->filled('session_name'), fn ($q) => $q->where('session_name', $request->session_name))
            ->when($request->filled('exam_name'), fn ($q) => $q->where('exam_name', $request->exam_name))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%'.$request->string('search')->trim().'%';

                $q->where(function ($query) use ($search) {
                    $query->where('student_id_number', 'like', $search)
                        ->orWhereHas('student', fn ($student) => $student->where('student_name', 'like', $search));
                });
            })
            ->orderBy('seat_number');
    }


    public function index(Request $request)
    {
        $school = $this->getSchool();
        $query = $school ? $this->filteredQuery($request, $school) : SchoolExamSeatPlan::query()->whereKey(0);

        // Print Data
        if ($request->boolean('all')) {

            $data = $query->get()->map(function ($seat) {
                return [
                    'id' => $seat->id,
                    'student_id_number' => $seat->student_id_number,
                    'student_name' => optional($seat->student)->student_name,
                    'seat_number' => $seat->seat_number,
                    'class_name' => $seat->class_name,
                    'group_name' => $seat->group_name,
                    'section_name' => $seat->section_name,
                    'session_name' => $seat->session_name,
                    'exam_name' => $seat->exam_name,
                ];
            });

            $schoolData = $school ? [
                'school_name' => $school->school_name,
                'logo' => $school->logo ? asset('storage/' . $school->logo) : null,
                'location' => trim($school->upazila . ', ' . $school->division, ', '),
                'upazila' => $school->upazila,
                'division' => $school->division,
                'mobile' => $school->mobile,
                'email' => $school->email,
            ] : null;

            return response()->json([
                'data' => $data,
                'school' => $schoolData,
            ]);
        }

        $data = $query->paginate(10);

        // Add student_name in pagination response
        $data->getCollection()->transform(function ($seat) {
            $seat->student_name = optional($seat->student)->student_name;
            unset($seat->student);

            return $seat;
        });

        return response()->json($data);
    }

    public function exportPdf(Request $request)
    {
        $school = $this->getSchool();

        abort_unless($school, 403, 'School profile not found.');

        $items = $this->filteredQuery($request, $school)->get()->map(function ($seat) {
            return [
                'student_id_number' => $seat->student_id_number,
                'student_name' => optional($seat->student)->student_name,
                'seat_number' => $seat->seat_number,
                'class_name' => $seat->class_name,
                'group_name' => $seat->group_name,
                'section_name' => $seat->section_name,
                'session_name' => $seat->session_name,
                'exam_name' => $seat->exam_name,
            ];
        });

        $pdf = Pdf::loadView('exports.seat_plan_pdf', [
            'school' => $school,
            'items' => $items,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('seat-plans-'.now()->format('Ymd-His').'.pdf');
    }

    public function preview(Request $request)
    {
        $school = $this->getSchool();
        abort_unless($school, 403, 'School profile not found.');

        $seat = SchoolExamSeatPlan::with('student:id,student_id_number,student_name')
            ->where('school_id', $school->id)
            ->findOrFail($request->integer('seat_plan_id'));

        return view('exports.seat_plan_pdf', [
            'school' => $school,
            'items' => collect([[
                'student_id_number' => $seat->student_id_number,
                'student_name' => optional($seat->student)->student_name,
                'seat_number' => $seat->seat_number,
                'class_name' => $seat->class_name,
                'group_name' => $seat->group_name,
                'section_name' => $seat->section_name,
                'session_name' => $seat->session_name,
                'exam_name' => $seat->exam_name,
            ]]),
            'preview' => true,
        ]);
    }

    public function store(Request $request)
    {
        $school = $this->getSchool();
        $school_id = $school ? $school->id : null;
        $startNum = (int) $request->seat_number_start;
        $examName = $request->exam_name;
        $generationMode = $request->input('generation_mode', 'single');
        $isMultiClass = $generationMode === 'multi' || count($this->normalizeIds($request->input('class_ids'))) > 1;

        if (!$school_id || !$examName || !$startNum) {
            return response()->json(['message' => 'Please select an exam and starting seat number.'], 422);
        }

        $classIds = $this->resolveSelectedIds($request, 'class_ids', 'class_names', SchoolClass::class, 'class_name', $school_id);
        $groupIds = $this->resolveSelectedIds($request, 'group_ids', 'group_names', SchoolGroup::class, 'group_name', $school_id);
        $sectionIds = $this->resolveSelectedIds($request, 'section_ids', 'section_names', SchoolSection::class, 'section_name', $school_id);
        $sessionIds = $this->resolveSelectedIds($request, 'session_ids', 'session_names', SchoolSession::class, 'session_year', $school_id);

        if (empty($classIds) || empty($sessionIds)) {
            return response()->json(['message' => 'Please select at least one class and one session.'], 422);
        }

        $schoolScopeIds = $this->getSchoolScopeIds($school);
        $query = DB::table('admission_students');

        if (!empty($schoolScopeIds)) {
            $query->where(function ($q) use ($schoolScopeIds) {
                foreach ($schoolScopeIds as $scopeSchoolId) {
                    $q->orWhere('school_id', $scopeSchoolId);
                }
            });
        }

        if (!empty($classIds)) {
            $query->whereIn('class_id', $classIds);
        }
        if (!empty($groupIds)) {
            $query->whereIn('group_id', $groupIds);
        }
        if (!empty($sectionIds)) {
            $query->whereIn('section_id', $sectionIds);
        }
        if (!empty($sessionIds)) {
            $query->whereIn('session_id', $sessionIds);
        }

        $query->where(function ($q) {
            $q->whereNull('status')
                ->orWhereRaw("LOWER(COALESCE(status, '')) NOT IN ('inactive', 'tc', 'transfer certificate', 'transfer_certificate', 'dropout', 'deleted')");
        });

        $students = $query->select('id', 'student_name', 'student_id_number', 'class_id', 'group_id', 'section_id', 'session_id', 'status')
            ->orderBy('student_name', 'asc')
            ->get();

        if ($students->isEmpty()) {
            return response()->json(['message' => 'No eligible students found for the selected filters.'], 422);
        }

        $classNameLookup = SchoolClass::where('school_id', $school_id)->pluck('class_name', 'id');
        $groupNameLookup = SchoolGroup::where('school_id', $school_id)->pluck('group_name', 'id');
        $sectionNameLookup = SchoolSection::where('school_id', $school_id)->pluck('section_name', 'id');
        $sessionNameLookup = SchoolSession::where('school_id', $school_id)->pluck('session_year', 'id');

        $studentRows = $students->map(function ($student) use ($classNameLookup, $groupNameLookup, $sectionNameLookup, $sessionNameLookup) {
            return [
                'id' => $student->id,
                'student_name' => $student->student_name,
                'student_id_number' => $student->student_id_number,
                'class' => $student->class_id,
                'group' => $student->group_id,
                'section' => $student->section_id,
                'session' => $student->session_id,
                'class_name' => $classNameLookup[$student->class_id] ?? null,
                'group_name' => $groupNameLookup[$student->group_id] ?? null,
                'section_name' => $sectionNameLookup[$student->section_id] ?? null,
                'session_name' => $sessionNameLookup[$student->session_id] ?? null,
                'status' => $student->status,
            ];
        })->all();

        $generator = new SeatPlanGenerator();
        $assignments = $generator->buildAssignments($studentRows, $startNum, $isMultiClass);

        DB::beginTransaction();
        try {
            foreach ($assignments as $index => $assignment) {
                $studentId = $assignment['student_id_number'];
                $studentRow = collect($studentRows)->firstWhere('student_id_number', $studentId);

                $exists = SchoolExamSeatPlan::where([
                    'school_id' => $school_id,
                    'exam_name' => $examName,
                    'student_id_number' => $studentId,
                ])->exists();

                if ($exists) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'exists',
                        'message' => "Seat Plan already exists for Student ID: {$studentId} in this exam. Please delete previous record to regenerate."
                    ], 422);
                }

                SchoolExamSeatPlan::create([
                    'school_id' => $school_id,
                    'class_name' => $studentRow['class_name'] ?? null,
                    'group_name' => $studentRow['group_name'] ?? null,
                    'section_name' => $studentRow['section_name'] ?? null,
                    'session_name' => $studentRow['session_name'] ?? null,
                    'exam_name' => $examName,
                    'student_id_number' => $studentId,
                    'seat_number' => $assignment['seat_number'],
                    'seat_number_start' => $request->seat_number_start,
                    'seat_number_end' => $request->seat_number_end ?: ($startNum + count($assignments) - 1),
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Seat plans generated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $school = $this->getSchool();
        $school_id = $school ? $school->id : null;
        $seat = SchoolExamSeatPlan::where('school_id', $school_id)->findOrFail($id);

        $seat->update([
            'seat_number' => $request->seat_number,
        ]);

        return response()->json(['message' => 'Seat number updated successfully']);
    }

    public function destroy($id)
    {
        $school = $this->getSchool();
        $school_id = $school ? $school->id : null;
        SchoolExamSeatPlan::where('school_id', $school_id)->where('id', $id)->delete();
        return response()->json(['message' => 'Record deleted successfully']);
    }
}
