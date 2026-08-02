<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\SchoolMembership;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SchoolIncomeController extends Controller
{
    protected $smsService;

    /**
     * Inject the SmsService via Constructor
     */
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Helper to get school info based on the authenticated user
     */
    private function getSchool($user)
    {
        return DB::table('schools')->where('user_id', $user->id)->first();
    }

    /**
     * Consistent query builder for index and export
     */
    private function getIncomeQuery($schoolId, $search = null)
    {
        $query = Income::where('school_id', $schoolId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('income_source', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('member_no', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * AJAX Endpoint for Select2 Search
     */
    public function membershipList(Request $request)
    {
        $school = $this->getSchool($request->user());
        if (!$school) return response()->json([]);

        $search = $request->search;

        $members = SchoolMembership::where('school_id', $school->id)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('member_no', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('mobile_number', 'like', "%{$search}%");
                });
            })
            ->limit(10)
            ->get(['id', 'member_no', 'name', 'mobile_number', 'income_source', 'amount', 'address']);

        return response()->json($members);
    }

    /**
     * Display a listing of incomes
     */
    public function index(Request $request)
    {
        $school = $this->getSchool($request->user());
        if (!$school) return response()->json(['data' => []]);

        $query = $this->getIncomeQuery($school->id, $request->search);

        return response()->json($query->latest()->paginate(10));
    }

    /**
     * Store a newly created income and send SMS
     */
    public function store(Request $request)
    {
        $school = $this->getSchool($request->user());
        if (!$school) return response()->json(['message' => 'School not found'], 404);

        $request->validate([
            'date'          => 'required|date',
            'income_source' => 'required|string',
            'name'          => 'required|string',
            'mobile'        => 'required|string',
            'amount'        => 'required|numeric',
            'address'       => 'nullable|string',
            'member_no'     => 'nullable|string'
        ]);

        try {
            $income = Income::create([
                'school_id'     => $school->id,
                'date'          => $request->date,
                'income_source' => $request->income_source,
                'name'          => $request->name,
                'mobile'        => $request->mobile,
                'amount'        => $request->amount,
                'address'       => $request->address,
                'member_no'     => $request->member_no
            ]);

            // Automatically send SMS on save using the Service
            $smsResponse = $this->sendThankYouSMS($income, $school);

            // Handle customized error message if credits are insufficient
            $smsStatusMessage = $smsResponse['success'] ? 'SMS Sent successfully' : 'SMS Failed: ' . $smsResponse['message'];

            // Check if the service specifically reported insufficient balance or expiry
            if (!$smsResponse['success'] && str_contains(strtolower($smsResponse['message']), 'expired or insufficient')) {
                $smsStatusMessage = "You have not enough credits to send SMS. Please buy an SMS package and try again.";
            }

            return response()->json([
                'message'    => 'Income added successfully',
                'income'     => $income,
                'sms_status' => [
                    'success' => $smsResponse['success'],
                    'message' => $smsStatusMessage,
                    'details' => $smsResponse
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        return response()->json(Income::findOrFail($id));
    }

    /**
     * Update an income
     */
    public function update(Request $request, $id)
    {
        $income = Income::findOrFail($id);
        $data = $request->validate([
            'date'          => 'required|date',
            'income_source' => 'required|string',
            'name'          => 'required|string',
            'mobile'        => 'required|string',
            'amount'        => 'required|numeric',
            'address'       => 'nullable|string',
            'member_no'     => 'nullable|string'
        ]);

        $income->update($data);
        return response()->json(['message' => 'Updated successfully']);
    }

    /**
     * Delete an income
     */
    public function destroy($id)
    {
        Income::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    /**
     * Manual Resend SMS
     */
    public function resendSms(Request $request, $id)
    {
        $income = Income::findOrFail($id);
        $school = $this->getSchool($request->user());

        if (!$school || $income->school_id != $school->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $smsResponse = $this->sendThankYouSMS($income, $school);

        $smsStatusMessage = $smsResponse['success'] ? 'SMS Sent' : 'SMS Failed: ' . $smsResponse['message'];

        if (!$smsResponse['success'] && str_contains(strtolower($smsResponse['message']), 'expired or insufficient')) {
            $smsStatusMessage = "You have not enough credits to send SMS. Please buy an SMS package and try again.";
        }

        return response()->json([
            'message'    => $smsStatusMessage,
            'sms_status' => [
                'success' => $smsResponse['success'],
                'message' => $smsStatusMessage
            ]
        ]);
    }

    /**
     * Individual Receipt (PDF)
     */
    public function receipt($id)
    {
        $income = Income::findOrFail($id);
        $school = DB::table('schools')->where('id', $income->school_id)->first();

        if (!$school) abort(404, "School data not found.");

        $pdf = Pdf::loadView('exports.income_receipt', compact('income', 'school'))
            ->setPaper([0, 0, 400, 600], 'portrait');

        return $pdf->stream("receipt_{$id}.pdf");
    }

    /**
     * Export Full List (PDF)
     */
    public function export(Request $request)
    {
        $school = $this->getSchool($request->user());
        if (!$school) return response()->json(['message' => 'Unauthorized'], 403);

        $query = $this->getIncomeQuery($school->id, $request->query('search'));
        $incomes = $query->latest()->get();

        $pdf = Pdf::loadView('exports.income_list_pdf', [
            'incomes' => $incomes,
            'school' => $school,
            'date' => now()->format('j-F-Y')
        ]);

        return $pdf->download("income_report_" . now()->format('Ymd') . ".pdf");
    }

    /**
     * Core SMS Logic - Now leveraging the Reusable Service
     */
    private function sendThankYouSMS($income, $school)
    {
        $dateInstance = Carbon::parse($income->date);
        $month = $dateInstance->format('F');
        $year = $dateInstance->format('Y');
        $institutionName = $school->school_name ?? 'আমাদের প্রতিষ্ঠান';

        $defaultMessage = "জনাব, {student_name}\nআপনি {school_name}-এ {income_source} বাবদ {month} মাস {year} সাল এর {fee_amount} টাকা প্রদান করেছেন। ধন্যবাদ।";

        return $this->smsService->triggerEventSms($school, $income->mobile, 'income', [
            '{student_name}' => $income->name,
            '{school_name}' => $institutionName,
            '{income_source}' => $income->income_source,
            '{month}' => $month,
            '{year}' => $year,
            '{fee_amount}' => $income->amount
        ], $defaultMessage);
    }
}
