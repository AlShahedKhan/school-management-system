<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscountRequest;
use App\Http\Requests\UpdateDiscountRequest;
use App\Models\AdmissionStudent;
use App\Models\DiscountStudent;
use App\Models\School;
use App\Models\SchoolDiscount;
use App\Models\SchoolFeeTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolDiscountController extends Controller
{
    private function getSchool(Request $request): ?School
    {
        return School::where('user_id', $request->user()->id)->first();
    }

    private function calculateDiscount(float $baseAmount, string $type, float $value): array
    {
        if ($type === 'Percentage') {
            $afterDiscount = $baseAmount - ($baseAmount * $value / 100);
        } else {
            $afterDiscount = $baseAmount - $value;
        }

        $afterDiscount = max(0, $afterDiscount); // never negative
        $discountAmount = $baseAmount - $afterDiscount;

        return [
            'afterDiscount' => round($afterDiscount, 2),
            'discountAmount' => round($discountAmount, 2),
        ];
    }

    private function buildStudentQuery(array $filters): \Illuminate\Database\Eloquent\Builder
    {
        $q = AdmissionStudent::where('school_id', $filters['school_id'])
            ->where('class_id', $filters['class_id'])
            ->where('session_id', $filters['session_id']);

        if (!empty($filters['group_id'])) {
            $q->where('group_id', $filters['group_id']);
        }
        if (!empty($filters['section_id'])) {
            $q->where('section_id', $filters['section_id']);
        }

        return $q;
    }
    public function index(Request $request): JsonResponse
    {
        $school = $this->getSchool($request);
        if (!$school) {
            return response()->json(['status' => 'error', 'message' => 'School not found.'], 404);
        }

        try {
            $query = SchoolDiscount::with([
                'schoolClass',
                'schoolGroup',
                'schoolSection',
                'schoolSession',
                'feeTemplate',
                'exam',
                'createdBy',
            ])
                ->withCount('discountStudents')
                ->where('school_id', $school->id);

            if ($request->filled('class_id')) {
                $query->where('class_id', $request->class_id);
            }
            if ($request->filled('group_id')) {
                $query->where('group_id', $request->group_id);
            }
            if ($request->filled('section_id')) {
                $query->where('section_id', $request->section_id);
            }
            if ($request->filled('session_id')) {
                $query->where('session_id', $request->session_id);
            }

            if ($request->filled('discount_category')) {
                $query->where('discount_category', $request->discount_category);
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Search by fee template name or category
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas(
                        'feeTemplate',
                        fn($sq) =>
                        $sq->where('fee_type_name', 'like', "%{$search}%")
                            ->orWhere('fee_name', 'like', "%{$search}%")
                    )
                        ->orWhere('discount_category', 'like', "%{$search}%");
                });
            }

            $query->latest();

            $result = $request->boolean('all')
                ? ['data' => $query->get()]
                : $query->paginate($request->integer('per_page', 10));

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Discount index error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch discounts.',
            ], 500);
        }
    }

    public function store(StoreDiscountRequest $request): JsonResponse
    {
        $school = $this->getSchool($request);
        if (!$school) {
            return response()->json(['status' => 'error', 'message' => 'School not found.'], 404);
        }

        $validated = $request->validated();

        // Fetch fee template and calculate amounts
        $feeTemplate = SchoolFeeTemplate::find($validated['fee_template_id']);
        if (!$feeTemplate) {
            return response()->json(['status' => 'error', 'message' => 'Fee template not found.'], 404);
        }

        $baseAmount = (float) $feeTemplate->amount;
        $calc = $this->calculateDiscount($baseAmount, $validated['discount_type'], (float) $validated['discount_value']);

        try {
            $discount = DB::transaction(function () use ($validated, $school, $request, $baseAmount, $calc) {
                // 1. Create the discount rule
                $discount = SchoolDiscount::create([
                    'school_id' => $school->id,
                    'created_by' => $request->user()->id,
                    'class_id' => $validated['class_id'],
                    'group_id' => $validated['group_id'] ?? null,
                    'section_id' => $validated['section_id'] ?? null,
                    'session_id' => $validated['session_id'],
                    'discount_category' => $validated['discount_category'],
                    'discount_type' => $validated['discount_type'],
                    'discount_value' => $validated['discount_value'],
                    'fee_template_id' => $validated['fee_template_id'],
                    'student_scope' => $validated['student_scope'],
                    'auto_apply_new_students' => $validated['auto_apply_new_students'] ?? false,
                    'exam_id' => $validated['exam_id'] ?? null,
                    'min_gpa' => $validated['min_gpa'] ?? null,
                    'min_marks' => $validated['min_marks'] ?? null,
                    'months' => $validated['months'] ?? null,
                    'is_active' => true,
                ]);

                // 2. Resolve eligible students
                if ($validated['student_scope'] === 'selected') {
                    $students = AdmissionStudent::whereIn('id', $validated['student_ids'] ?? [])->get();
                } else {
                    $students = $this->buildStudentQuery([
                        'school_id' => $school->id,
                        'class_id' => $validated['class_id'],
                        'session_id' => $validated['session_id'],
                        'group_id' => $validated['group_id'] ?? null,
                        'section_id' => $validated['section_id'] ?? null,
                    ])->get();
                }

                // 3. Create discount_student records
                foreach ($students as $student) {
                    DiscountStudent::create([
                        'discount_id' => $discount->id,
                        'student_id' => $student->id,
                        'before_amount' => $baseAmount,
                        'after_amount' => $calc['afterDiscount'],
                        'status' => 'active',
                    ]);
                }

                return $discount;
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Discount created successfully.',
                'data' => $discount->load(['discountStudents.student', 'feeTemplate']),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Discount store error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create discount.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function show(Request $request, int $id): JsonResponse
    {
        $school = $this->getSchool($request);
        if (!$school) {
            return response()->json(['status' => 'error', 'message' => 'School not found.'], 404);
        }

        try {
            $discount = SchoolDiscount::with([
                'schoolClass',
                'schoolGroup',
                'schoolSection',
                'schoolSession',
                'feeTemplate',
                'exam',
                'createdBy',
                'discountStudents.student',
            ])
                ->where('school_id', $school->id)
                ->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $discount,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Discount not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Discount show error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to fetch discount.'], 500);
        }
    }
    public function update(UpdateDiscountRequest $request, int $id): JsonResponse
    {
        $school = $this->getSchool($request);
        if (!$school) {
            return response()->json(['status' => 'error', 'message' => 'School not found.'], 404);
        }

        $validated = $request->validated();

        try {
            $discount = SchoolDiscount::where('school_id', $school->id)->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Discount not found.'], 404);
        }

        try {
            $updatedDiscount = DB::transaction(function () use ($discount, $validated, $request, $school) {
                // 1. Update the discount rule
                $discount->update($validated);

                // 2. Re-sync students if any scoping field changed
                $resyncFields = ['student_scope', 'class_id', 'group_id', 'section_id', 'session_id', 'student_ids'];
                if ($request->hasAny($resyncFields)) {
                    $scope = $validated['student_scope'] ?? $discount->student_scope;
                    $classId = $validated['class_id'] ?? $discount->class_id;
                    $groupId = $validated['group_id'] ?? $discount->group_id;
                    $sectionId = $validated['section_id'] ?? $discount->section_id;
                    $sessionId = $validated['session_id'] ?? $discount->session_id;

                    // Resolve fee amounts
                    $templateId = $validated['fee_template_id'] ?? $discount->fee_template_id;
                    $template = SchoolFeeTemplate::find($templateId);
                    $baseAmount = $template ? (float) $template->amount : 0;

                    $discountType = $validated['discount_type'] ?? $discount->discount_type;
                    $discountValue = $validated['discount_value'] ?? $discount->discount_value;
                    $calc = $this->calculateDiscount($baseAmount, $discountType, (float) $discountValue);

                    // Remove old assignments
                    DiscountStudent::where('discount_id', $discount->id)->delete();

                    // Resolve students
                    if ($scope === 'selected') {
                        $students = AdmissionStudent::whereIn('id', $validated['student_ids'] ?? [])->get();
                    } else {
                        $students = $this->buildStudentQuery([
                            'school_id' => $school->id,
                            'class_id' => $classId,
                            'session_id' => $sessionId,
                            'group_id' => $groupId,
                            'section_id' => $sectionId,
                        ])->get();
                    }

                    // Re-create assignments
                    foreach ($students as $student) {
                        DiscountStudent::create([
                            'discount_id' => $discount->id,
                            'student_id' => $student->id,
                            'before_amount' => $baseAmount,
                            'after_amount' => $calc['afterDiscount'],
                            'status' => 'active',
                        ]);
                    }
                }

                return $discount->fresh()->load(['discountStudents.student', 'feeTemplate']);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Discount updated successfully.',
                'data' => $updatedDiscount,
            ]);

        } catch (\Exception $e) {
            Log::error('Discount update error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update discount.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function destroy(Request $request, int $id): JsonResponse
    {
        $school = $this->getSchool($request);
        if (!$school) {
            return response()->json(['status' => 'error', 'message' => 'School not found.'], 404);
        }

        try {
            $discount = SchoolDiscount::where('school_id', $school->id)->findOrFail($id);

            DB::transaction(function () use ($discount) {
                DiscountStudent::where('discount_id', $discount->id)->delete();
                $discount->delete();
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Discount deleted successfully.',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Discount not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Discount destroy error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete discount.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
