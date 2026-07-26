<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreFeeTemplateRequest;
use App\Http\Requests\UpdateFeeTemplateRequest;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateMonthlyFeesForTemplate;
use App\Models\AdmissionStudent;
use App\Models\SchoolFeeTemplate;
use App\Models\SchoolFeeType;
use App\Models\SchoolStudentFee;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SchoolFeeTemplateController extends Controller
{
    private function getSchool($user)
    {
        return School::where('user_id', $user->id)->first();
    }

    public function index(Request $request)
    {
        try {
            $school = $this->getSchool($request->user());

            $query = SchoolFeeTemplate::with([
                'schoolClass',
                'schoolGroup',
                'schoolSection',
                'schoolSession',
                'schoolExam',
            ])->where('school_id', $school->id);

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

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('fee_type_name', 'like', "%{$search}%")
                        ->orWhere('fee_name', 'like', "%{$search}%")
                        ->orWhere('amount', 'like', "%{$search}%")
                        ->orWhereHas('schoolClass', function ($sq) use ($search) {
                            $sq->where('class_name', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->boolean('all')) {
                $results = $query->latest()->get();
                return response()->json(['data' => $results]);
            }

            $results = $query->latest()->paginate($request->input('per_page', 30));
            return response()->json($results);

        } catch (\Exception $e) {
            Log::error('FeeTemplate index error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load fee templates. Please try again.',
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $school = $this->getSchool($request->user());

            $template = SchoolFeeTemplate::with([
                'schoolClass',
                'schoolGroup',
                'schoolSection',
                'schoolSession',
                'schoolExam',
            ])->where('school_id', $school->id)->findOrFail($id);

            return response()->json($template);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Fee template not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('FeeTemplate show error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch fee template.',
            ], 500);
        }
    }

    public function store(StoreFeeTemplateRequest $request)
    {
        try {
            $school = $this->getSchool($request->user());
            $validated = $request->validated();

            $validated['frequency'] = $validated['frequency'] ?? match ($validated['fee_type_name']) {
                'Tuition', 'Food', 'Fine' => 'monthly',
                'Exams' => 'per_exam',
                default => 'one_time',
            };

            if ($validated['frequency'] === 'one_time' && empty($validated['pay_date'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed. Please check the form fields.',
                    'errors' => ['pay_date' => ['Pay date is required for one-time fees.']],
                ], 422);
            }

            $result = DB::transaction(function () use ($school, $validated) {
                $template = SchoolFeeTemplate::create([
                    'school_id' => $school->id,
                    'class_id' => $validated['class_id'],
                    'session_id' => $validated['session_id'],
                    'group_id' => $validated['group_id'] ?? null,
                    'section_id' => $validated['section_id'] ?? null,
                    'fee_type_name' => $validated['fee_type_name'],
                    'fee_name' => $validated['fee_name'] ?? null,
                    'exam_id' => $validated['exam_id'] ?? null,
                    'amount' => $validated['amount'],
                    'pay_date' => $validated['pay_date'],
                    'frequency' => $validated['frequency'],
                    'due_day' => $validated['due_day'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'is_active' => true,
                    'food_type' => $validated['food_type'] ?? null,
                    'student_ids' => $validated['student_ids'] ?? null,
                ]);

                $created = 0;

                $generateFees = $validated['frequency'] === 'one_time' || $validated['fee_type_name'] === 'Food';

                if ($generateFees) {
                    if ($validated['fee_type_name'] === 'Food' && !empty($validated['student_ids'])) {
                        // Modified on 2026-07-09: Exclude Inactive students from food fee templates
                        $students = AdmissionStudent::whereIn('id', $validated['student_ids'])
                            ->where('status', '!=', 'Inactive')
                            ->get(['id']);
                    } else {
                        // Modified on 2026-07-09: Exclude Inactive students from class fee templates
                        $studentQuery = AdmissionStudent::where('school_id', $school->user_id)
                            ->where('status', '!=', 'Inactive')
                            ->where('class', $validated['class_id'])
                            ->where('session', $validated['session_id']);

                        if (!empty($validated['group_id'])) {
                            $studentQuery->where('group', $validated['group_id']);
                        }
                        if (!empty($validated['section_id'])) {
                            $studentQuery->where('section', $validated['section_id']);
                        }

                        $students = $studentQuery->get(['id']);
                    }

                    if ($students->isNotEmpty()) {
                        $rows = [];
                        $now = now();
                        foreach ($students as $student) {
                            $alreadyExists = SchoolStudentFee::where('school_id', $school->id)
                                ->where('student_id', $student->id)
                                ->where('fee_type_name', $validated['fee_type_name'])
                                ->where('fee_name', $validated['fee_name'] ?? null)
                                ->exists();

                            if ($alreadyExists) {
                                continue;
                            }

                            $rows[] = [
                                'school_id'       => $school->id,
                                'student_id'      => $student->id,
                                'fee_template_id' => $template->id,
                                'fee_type_name'   => $validated['fee_type_name'],
                                'fee_name'        => $validated['fee_name'] ?? null,
                                'amount'          => $validated['amount'],
                                'payable_amount'  => $validated['amount'],
                                'due_amount'      => $validated['amount'],
                                'pay_date'        => $validated['pay_date'],
                                'due_date'        => $validated['pay_date'],
                                'status'          => 'unpaid',
                                'created_at'      => $now,
                                'updated_at'      => $now,
                            ];
                            $created++;
                        }

                        if (!empty($rows)) {
                            SchoolStudentFee::insert($rows);
                        }
                    }
                }

                return compact('created');
            });

            $message = "Fee template created successfully.";
            if ($result['created'] > 0) {
                $message .= " {$result['created']} student fee record(s) auto-generated.";
            } elseif ($validated['frequency'] !== 'one_time' && $validated['fee_type_name'] !== 'Food') {
                $message .= " Student fees will be generated per schedule (frequency: {$validated['frequency']}).";
            }

            return response()->json([
                'status' => 'success',
                'message' => $message,
            ], 201);

        } catch (\Exception $e) {
            Log::error('FeeTemplate store error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while saving the fee template. Please try again.',
            ], 500);
        }
    }

    public function update(UpdateFeeTemplateRequest $request, $id)
    {
        try {
            $school = $this->getSchool($request->user());
            $template = SchoolFeeTemplate::where('school_id', $school->id)->findOrFail($id);
            $validated = $request->validated();

            $validated['frequency'] = $validated['frequency'] ?? match ($validated['fee_type_name']) {
                'Tuition', 'Food', 'Fine' => 'monthly',
                'Exams' => 'per_exam',
                default => 'one_time',
            };

            if ($validated['frequency'] === 'one_time' && empty($validated['pay_date'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed. Please check the form fields.',
                    'errors' => ['pay_date' => ['Pay date is required for one-time fees.']],
                ], 422);
            }

            DB::transaction(function () use ($template, $validated) {
                $amountChanged = $template->amount != $validated['amount'];
                $payDateChanged = $template->pay_date != $validated['pay_date'];

                $template->update([
                    'class_id' => $validated['class_id'],
                    'session_id' => $validated['session_id'],
                    'group_id' => $validated['group_id'] ?? null,
                    'section_id' => $validated['section_id'] ?? null,
                    'fee_type_name' => $validated['fee_type_name'],
                    'fee_name' => $validated['fee_name'] ?? null,
                    'exam_id' => $validated['exam_id'] ?? null,
                    'amount' => $validated['amount'],
                    'pay_date' => $validated['pay_date'],
                    'frequency' => $validated['frequency'],
                    'due_day' => $validated['due_day'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'food_type' => $validated['food_type'] ?? null,
                    'student_ids' => !empty($validated['student_ids']) ? json_encode($validated['student_ids']) : null,
                ]);

                if ($amountChanged || $payDateChanged) {
                    $updateData = [];
                    if ($amountChanged)
                        $updateData['amount'] = $validated['amount'];
                    if ($payDateChanged)
                        $updateData['pay_date'] = $validated['pay_date'];

                    if (!empty($updateData)) {
                        SchoolStudentFee::where('fee_template_id', $template->id)
                            ->whereNotIn('status', ['paid'])
                            ->update($updateData);
                    }
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Fee template updated successfully.',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Fee template not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('FeeTemplate update error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while updating the fee template. Please try again.',
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $school = $this->getSchool($request->user());
            $template = SchoolFeeTemplate::where('school_id', $school->id)->findOrFail($id);

            DB::transaction(function () use ($template) {
                SchoolStudentFee::where('fee_template_id', $template->id)
                    ->where('status', 'pending')
                    ->delete();

                $template->delete();
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Fee template deleted successfully.',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Fee template not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('FeeTemplate destroy error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while deleting the fee template.',
            ], 500);
        }
    }

    public function generateStudentFees(Request $request, $id)
    {
        try {
            $school = $this->getSchool($request->user());
            $template = SchoolFeeTemplate::where('school_id', $school->id)->findOrFail($id);

            $lock = Cache::lock("generate-fees-{$id}", 10);
            if (!$lock->get()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Fee generation is already queued for this template. Please wait.',
                ], 429);
            }

            GenerateMonthlyFeesForTemplate::dispatch($template->id);

            return response()->json([
                'status' => 'success',
                'message' => "Fee generation queued for template #{$template->id}.",
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Fee template not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('FeeTemplate generateStudentFees error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to queue fee generation. Please try again.',
            ], 500);
        }
    }

    public function backfillFromLegacy()
    {
        try {
            if (!Schema::hasTable('school_fee_types')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Legacy table school_fee_types does not exist. Nothing to backfill.',
                ]);
            }

            $now = now();
            $templatesCreated = 0;
            $studentFeesCreated = 0;

            DB::transaction(function () use ($now, &$templatesCreated, &$studentFeesCreated) {
                $templateRows = DB::table('school_fee_types')
                    ->whereNull('student_id')
                    ->get();

                $oldToNewMap = [];
                foreach ($templateRows as $row) {
                    $exists = DB::table('school_fee_templates')
                        ->where('school_id', $row->school_id)
                        ->where('class_id', $row->class_id)
                        ->where('group_id', $row->group_id)
                        ->where('section_id', $row->section_id)
                        ->where('session_id', $row->session_id)
                        ->where('fee_type_name', $row->fee_type_name)
                        ->where('fee_name', $row->fee_name)
                        ->where('exam_id', $row->exam_id ?? null)
                        ->exists();

                    if ($exists)
                        continue;

                    $frequency = 'one_time';
                    if (in_array($row->fee_type_name, ['Tuition', 'Monthly', 'Food'])) {
                        $frequency = 'monthly';
                    } elseif ($row->fee_type_name === 'Exams') {
                        $frequency = 'per_exam';
                    } elseif ($row->fee_type_name === 'Fine') {
                        $frequency = 'event_triggered';
                    }

                    $feeType = SchoolFeeType::create([
                        'school_id' => $row->school_id,
                        'class_id' => $row->class_id,
                        'group_id' => $row->group_id,
                        'section_id' => $row->section_id,
                        'session_id' => $row->session_id,
                        'fee_type_name' => $row->fee_type_name,
                        'fee_name' => $row->fee_name,
                        'exam_id' => $row->exam_id ?: null,
                        'pay_date' => $row->pay_date,
                        'frequency' => $frequency,
                        'due_day' => $frequency === 'monthly' ? 10 : null,
                        'amount' => $row->amount,
                        'description' => $row->description,
                        'is_active' => true,
                        'created_at' => $row->created_at ?? now(),
                        'updated_at' => $row->updated_at ?? now(),
                    ]);

                    $id = $feeType->id;

                    $oldToNewMap[$row->id] = $id;
                    $templatesCreated++;
                }

                $studentFeeRows = SchoolFeeType::whereNotNull('student_id')->get();

                foreach ($studentFeeRows as $row) {
                    $exists = SchoolFeeType::where('school_id', $row->school_id)
                        ->where('student_id', $row->student_id)
                        ->where('fee_type_name', $row->fee_type_name)
                        ->where('fee_name', $row->fee_name)
                        ->exists();

                    if ($exists)
                        continue;

                    $templateId = null;
                    if (isset($oldToNewMap[$row->id])) {
                        $templateId = $oldToNewMap[$row->id];
                    } else {
                        $templateId = SchoolFeeTemplate::where('school_id', $row->school_id)
                            ->where('class_id', $row->class_id)
                            ->where('group_id', $row->group_id)
                            ->where('section_id', $row->section_id)
                            ->where('session_id', $row->session_id)
                            ->where('fee_type_name', $row->fee_type_name)
                            ->where('fee_name', $row->fee_name)
                            ->value('id');
                    }

                    SchoolStudentFee::create([
                        'school_id' => $row->school_id,
                        'student_id' => $row->student_id,
                        'fee_template_id' => $templateId,
                        'fee_type_name' => $row->fee_type_name,
                        'fee_name' => $row->fee_name,
                        'amount' => $row->amount,
                        'pay_date' => $row->pay_date,
                        'status' => 'pending',
                        'created_at' => $row->created_at ?? now(),
                        'updated_at' => $row->updated_at ?? now(),
                    ]);

                    $studentFeesCreated++;
                }
            });

            $message = "Backfill complete: {$templatesCreated} template(s) and {$studentFeesCreated} student fee record(s) created.";

            if (request()->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'templates_created' => $templatesCreated,
                    'student_fees_created' => $studentFeesCreated,
                ]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('FeeTemplate backfillFromLegacy error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Backfill failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
