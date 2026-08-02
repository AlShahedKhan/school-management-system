<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\SchoolExpense;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AccountService;
use App\Http\Requests\StoreSchoolExpenseRequest;

class SchoolExpenseController extends Controller
{
    private function getSchool($user)
    {
        return DB::table('schools')->where('user_id', $user->id)->first();
    }

    public function index(Request $request)
    {
        $school = $this->getSchool($request->user());

        if (!$school) {
            return response()->json(['data' => []]);
        }

        $query = Expense::where('school_id', $school->id);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('expense_reason', 'like', "%{$request->search}%")
                    ->orWhere('name', 'like', "%{$request->search}%");
                // Mobile search removed
            });
        }

        return response()->json($query->latest()->paginate(10));
    }

    public function store(StoreSchoolExpenseRequest $request)
    {
        $school = $this->getSchool($request->user());
        if (!$school) {
            return response()->json([
                'message' => 'School profile not found.',
            ], 404);
        }
        try {
            DB::beginTransaction();
            $lastInvoice = SchoolExpense::where('school_id', $school->id)
                ->lockForUpdate()
                ->max('invoice_no');
            $invoiceNo = str_pad(((int) $lastInvoice) + 1, 8, '0', STR_PAD_LEFT);
            $expense = SchoolExpense::create([
                'school_id'      => $school->id,
                'invoice_no'     => $invoiceNo,
                'expense_date'   => $request->expense_date,
                'expense_reason' => $request->expense_reason,
                'amount'         => $request->amount,
                'balance'        => 0,
            ]);

            // Cash Out from the internal System Cash Balance (immutable ledger)
            app(AccountService::class)->cashOut(
                (int) $school->id,
                (float) $expense->amount,
                'Expense',
                $expense->id,
                [
                    'remarks' => $expense->expense_reason . ' | Invoice #' . $expense->invoice_no,
                ]
            );

            DB::commit();
            return response()->json([
                'message' => 'Expense recorded successfully.',
                'expense' => $expense,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'message' => 'Failed to record expense.',
            ], 500);
        }
    }

    public function show($id)
    {
        $expense = Expense::findOrFail($id);
        return response()->json($expense);
    }

    public function update(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $expense = Expense::where('school_id', $school->id)->findOrFail($id);

        // Updated to only include relevant fields (mobile excluded)
        $expense->update($request->only(['date', 'expense_reason', 'name', 'amount']));

        return response()->json(['message' => 'Expense updated successfully']);
    }

    public function destroy(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $expense = SchoolExpense::where('school_id', $school->id)->findOrFail($id);

        DB::transaction(function () use ($school, $expense) {
            $amount = (float) $expense->amount;
            $expenseId = $expense->id;
            $expense->delete();

            if ($amount > 0) {
                app(AccountService::class)->cashIn((int) $school->id, $amount, 'Expense Adjustment', $expenseId, [
                    'remarks' => 'Reversal of deleted expense #' . $expenseId,
                ]);
            }
        });

        return response()->json(['message' => 'Expense deleted']);
    }

    public function export(Request $request)
    {
        $school = $this->getSchool($request->user());
        $expenses = Expense::where('school_id', $school->id)->latest()->get();

        $filename = "expense_report_" . date('Y-m-d') . ".csv";

        $callback = function () use ($expenses) {
            $file = fopen('php://output', 'w');
            // Removed 'Mobile' from header
            fputcsv($file, ['Date', 'Reason', 'Name', 'Amount']);

            foreach ($expenses as $e) {
                // Removed $e->mobile from data rows
                fputcsv($file, [$e->date, $e->expense_reason, $e->name, $e->amount]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename"
        ]);
    }

}
