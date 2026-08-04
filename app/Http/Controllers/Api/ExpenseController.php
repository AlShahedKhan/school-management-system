<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\School;
use App\Services\AccountService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    private function getSchoolId()
    {
        $school = School::where('user_id', Auth::id())->first();
        return $school ? $school->id : null;
    }

    public function index(Request $request)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return response()->json(['message' => 'School not found'], 400);
        }

        $query = Expense::where('school_id', $schoolId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('expense_reason', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhere('date', 'like', "%{$search}%");
            });
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $expenses = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(30);

        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return response()->json(['message' => 'School not found'], 400);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'invoice_no' => 'nullable|string|max:255',
            'expense_reason' => 'required|string|max:255',
            'details' => 'nullable|string',
            'month' => 'nullable|string',
            'year' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $month = $request->month;
        $year = $request->year;
        if ((!$month || !$year) && $request->date) {
            $parsedDate = Carbon::parse($request->date);
            $month = $month ?: $parsedDate->format('F');
            $year = $year ?: $parsedDate->format('Y');
        }

        // Persist the expense and cash out of the internal System Cash Balance
        // atomically. If the ledger update fails, the expense rolls back.
        $expense = DB::transaction(function () use ($schoolId, $request, $month, $year) {
            $expense = Expense::create([
                'school_id' => $schoolId,
                'invoice_no' => $request->invoice_no,
                'date' => $request->date,
                'expense_reason' => $request->expense_reason,
                'details' => $request->details,
                'month' => $month,
                'year' => $year,

                'name' => $request->expense_reason,
                'amount' => $request->amount,
            ]);

            app(AccountService::class)->cashOut(
                $schoolId,
                (float) $expense->amount,
                'Expense',
                $expense->id,
                [
                    'remarks' => $expense->expense_reason . ($expense->details ? ' | ' . $expense->details : ''),
                ]
            );

            return $expense;
        });

        return response()->json([
            'message' => 'Expense registered successfully',
            'data' => $expense
        ], 201);
    }

    public function show($id)
    {
        $schoolId = $this->getSchoolId();
        $expense = Expense::where('school_id', $schoolId)->findOrFail($id);

        return response()->json([
            'data' => $expense
        ]);
    }

    public function update(Request $request, $id)
    {
        $schoolId = $this->getSchoolId();
        $expense = Expense::where('school_id', $schoolId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'invoice_no' => 'nullable|string|max:255',
            'expense_reason' => 'required|string|max:255',
            'details' => 'nullable|string',
            'month' => 'nullable|string',
            'year' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $month = $request->month;
        $year = $request->year;
        if ((!$month || !$year) && $request->date) {
            $parsedDate = Carbon::parse($request->date);
            $month = $month ?: $parsedDate->format('F');
            $year = $year ?: $parsedDate->format('Y');
        }

        // Keep the internal ledger consistent with immutable Reverse entries.
        DB::transaction(function () use ($schoolId, $expense, $request, $month, $year) {
            $oldAmount = (float) $expense->amount;

            $expense->update([
                'invoice_no' => $request->invoice_no,
                'date' => $request->date,
                'expense_reason' => $request->expense_reason,
                'details' => $request->details,
                'month' => $month,
                'year' => $year,
                'name' => $request->expense_reason,
                'amount' => $request->amount,
            ]);

            $newAmount = (float) $expense->amount;
            if (abs($newAmount - $oldAmount) > 0.0001) {
                $service = app(AccountService::class);
                $service->cashIn($schoolId, $oldAmount, 'Expense Adjustment', $expense->id, [
                    'remarks' => 'Reversal of expense #' . $expense->id
                        . ' (amount adjusted ' . number_format($oldAmount, 2) . ' -> ' . number_format($newAmount, 2) . ')',
                ]);
                $service->cashOut($schoolId, $newAmount, 'Expense', $expense->id, [
                    'remarks' => $expense->expense_reason . ' (adjusted)',
                ]);
            }
        });

        return response()->json([
            'message' => 'Expense updated successfully',
            'data' => $expense
        ]);
    }

    public function destroy($id)
    {
        $schoolId = $this->getSchoolId();
        $expense = Expense::where('school_id', $schoolId)->findOrFail($id);

        DB::transaction(function () use ($schoolId, $expense) {
            $amount = (float) $expense->amount;
            $expenseId = $expense->id;
            $expense->delete();

            if ($amount > 0) {
                app(AccountService::class)->cashIn($schoolId, $amount, 'Expense Adjustment', $expenseId, [
                    'remarks' => 'Reversal of deleted expense #' . $expenseId,
                ]);
            }
        });

        return response()->json([
            'message' => 'Expense deleted successfully'
        ]);
    }
}
