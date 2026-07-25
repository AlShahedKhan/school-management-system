<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdmissionStudent;
use App\Models\SchoolFeeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SchoolFeeTypeController extends Controller
{
    /**
     * Helper to get the school associated with the authenticated user.
     */
    private function getSchool($user)
    {
        return DB::table('schools')->where('user_id', $user->id)->first();
    }

    public function index(Request $request)
    {
        $school = $this->getSchool($request->user());

        $query = SchoolFeeType::with([
            'schoolClass',
            'schoolGroup',
            'schoolSection',
            'schoolSession',
            'schoolExam',
            'student'
        ])->where('school_id', $school->id);

        // new comment

        // --- NEW FILTERS FOR DYNAMIC LOADING ---
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('session_id')) {
            $query->where('session_id', $request->session_id);
        }
        // ---------------------------------------

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fee_type_name', 'like', "%{$search}%")
                    ->orWhere('fee_name', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhereHas('schoolClass', function ($sq) use ($search) {
                        $sq->where('class_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('student_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('schoolExam', function ($sq) use ($search) {
                        $sq->where('exam_name', 'like', "%{$search}%");
                    });
            });
        }

        

        // Determine if we should return all results or a paginated set
        if ($request->boolean('all')) {
            $results = $query->latest()->get();

            // Transform the collection directly
            $results->transform(function ($item) {
                return $this->formatFeeItem($item);
            });

            return response()->json(['data' => $results]);
        }

        // Default: Paginated results for the main table
        $results = $query->latest()->paginate($request->input('per_page', 30));

        $results->getCollection()->transform(function ($item) {
            return $this->formatFeeItem($item);
        });

        return response()->json($results);
    }

    /**
     * Helper to keep the transformation logic DRY
     */
    private function formatFeeItem($item)
    {
        $item->display_detail = $item->fee_name ?? $item->fee_type_name;

        if ($item->pay_date) {
            $item->pay_date_formatted = \Carbon\Carbon::parse($item->pay_date)->format('d-M-Y');
        } else {
            $item->pay_date_formatted = '---';
        }

        // Calculate total already paid by this student for this fee
        if ($item->student_id) {
            $item->total_paid = (float) DB::table('school_payments')
                ->where('school_id', $item->school_id)
                ->where('admission_student_id', $item->student_id)
                ->where('fees_type', $item->fee_type_name)
                ->where('fee_name', $item->fee_name)
                ->sum('type_amount');
        } else {
            $item->total_paid = 0;
        }

        return $item;
    }
    public function show(Request $request, $id)
    {
        $school = $this->getSchool($request->user());

        $feeType = SchoolFeeType::with([
            'schoolClass',
            'schoolGroup',
            'schoolSection',
            'schoolSession',
            'schoolExam',
            'student'
        ])
            ->where('school_id', $school->id)
            ->findOrFail($id);

        return response()->json($feeType);
    }

    public function store(Request $request)
    {
        $school = $this->getSchool($request->user());

        $validated = $request->validate([
            'class_id'      => 'required|exists:school_classes,id',
            'session_id'    => 'required|exists:school_sessions,id',
            'fee_type_name' => 'required|string',
            'amount'        => 'required|numeric|min:0',
            'pay_date'      => 'required|date',
            'group_id'      => 'nullable',
            'section_id'    => 'nullable',
            'fee_name'      => 'required_without_all:student_id,exam_id|nullable|string|max:255',
            'exam_id'       => 'nullable|exists:school_exam_names,id',
            'student_id'    => 'nullable|exists:admission_students,id',
        ]);

        // --- Step 1: Fetch all matching students ---
        $studentQuery = AdmissionStudent::where('school_id', $school->user_id)
            ->where('class',   $validated['class_id'])
            ->where('session', $validated['session_id']);

        if (!empty($validated['group_id'])) {
            $studentQuery->where('group', $validated['group_id']);
        }
        if (!empty($validated['section_id'])) {
            $studentQuery->where('section', $validated['section_id']);
        }

        $students = $studentQuery->get(['id', 'student_name']);

        if ($students->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No students found for the selected class, session, group, and section combination.'
            ], 422);
        }

        // --- Step 2: Prepare shared fee data ---
        $baseData = $this->prepareData($validated);
        $feeName  = $baseData['fee_name'] ?? null;

        // --- Step 3: Wrap insert logic in a transaction ---
        $result = DB::transaction(function () use ($school, $validated, $baseData, $feeName, $students) {

            // Fetch all already-existing student_ids for this fee in one query
            $existingStudentIds = SchoolFeeType::where('school_id',     $school->id)
                ->where('class_id',      $validated['class_id'])
                ->where('session_id',    $validated['session_id'])
                ->where('group_id',      $validated['group_id']   ?? null)
                ->where('section_id',    $validated['section_id'] ?? null)
                ->where('fee_type_name', $validated['fee_type_name'])
                ->where('fee_name',      $feeName)
                ->where('exam_id',       $baseData['exam_id']     ?? null)
                ->whereNotNull('student_id')
                ->pluck('student_id')
                ->flip(); // O(1) isset() lookup

            $now          = now();
            $rows         = [];
            $created      = 0;
            $skipped      = 0;
            $skippedNames = [];

            foreach ($students as $student) {
                if (isset($existingStudentIds[$student->id])) {
                    $skipped++;
                    $skippedNames[] = $student->student_name;
                    continue;
                }

                $rows[] = [
                    'school_id'     => $school->id,
                    'class_id'      => $validated['class_id'],
                    'session_id'    => $validated['session_id'],
                    'group_id'      => $validated['group_id']   ?? null,
                    'section_id'    => $validated['section_id'] ?? null,
                    'fee_type_name' => $validated['fee_type_name'],
                    'fee_name'      => $feeName,
                    'exam_id'       => $baseData['exam_id']     ?? null,
                    'student_id'    => $student->id,
                    'amount'        => $validated['amount'],
                    'pay_date'      => $validated['pay_date'],
                    'description'   => $validated['description'] ?? null,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];

                $created++;
            }

            if (!empty($rows)) {
                SchoolFeeType::insert($rows);
            }

            return compact('created', 'skipped', 'skippedNames');
        });

        // --- Step 4: Build response ---
        if ($result['created'] === 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'All ' . $result['skipped'] . ' student(s) already have this fee configured. No new records were created.',
            ], 422);
        }

        $message = "Fee created for {$result['created']} student(s).";
        if ($result['skipped'] > 0) {
            $message .= " {$result['skipped']} student(s) were skipped (already exist): "
                . implode(', ', $result['skippedNames']) . '.';
        }

        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'created' => $result['created'],
            'skipped' => $result['skipped'],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $feeType = SchoolFeeType::where('school_id', $school->id)->findOrFail($id);

        $validated = $request->validate([
            'class_id'      => 'required|exists:school_classes,id',
            'session_id'    => 'required|exists:school_sessions,id',
            'fee_type_name' => 'required|string',
            'amount'        => 'required|numeric|min:0',
            'pay_date'      => 'required|date',
            'group_id'      => 'nullable',
            'section_id'    => 'nullable',
            'fee_name'      => 'required_without:student_id|nullable|string|max:255',
            'exam_id'       => 'nullable|exists:school_exam_names,id',
            'student_id'    => 'nullable|exists:admission_students,id',
        ]);

        // Duplicate Check Logic (excluding the current ID)
        $exists = SchoolFeeType::where([
            'school_id'     => $school->id,
            'class_id'      => $validated['class_id'],
            'session_id'    => $validated['session_id'],
            'group_id'      => $validated['group_id'] ?? null,
            'section_id'    => $validated['section_id'] ?? null,
            'fee_type_name' => $validated['fee_type_name'],
            'exam_id'       => $validated['exam_id'] ?? null,
            'student_id'    => $validated['student_id'] ?? null,
        ])->where('id', '!=', $id)->exists();

        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Another configuration with these details already exists.'
            ], 422);
        }

        $data = $this->prepareData($validated);
        $feeType->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Configuration updated successfully'
        ]);
    }

    /**
     * Logic: Maps specific relational names into the 'fee_name' column
     */
    private function prepareData($validated)
    {
        $type = $validated['fee_type_name'];

        if ($type === 'Exams' && !empty($validated['exam_id'])) {
            $exam = DB::table('school_exam_names')->where('id', $validated['exam_id'])->first();
            $validated['fee_name'] = $exam->exam_name ?? 'Exam Fee';
            $validated['student_id'] = null;
        } elseif ($type === 'Boarding Food' && !empty($validated['student_id'])) {
            $student = DB::table('admission_students')->where('id', $validated['student_id'])->first();
            $validated['fee_name'] = $student->student_name ?? 'Student Boarding';
            $validated['exam_id'] = null;
        } else {
            $validated['exam_id'] = null;
            $validated['student_id'] = null;
        }

        return $validated;
    }

    public function destroy(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $feeType = SchoolFeeType::where('school_id', $school->id)->findOrFail($id);
        $feeType->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Fee configuration deleted'
        ]);
    }
}
