<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SmsPackagePurchase;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminSmSRequestApproveController extends Controller
{
    /**
     * Fetch all SMS purchase requests with relationships.
     */
    public function index()
    {
        $requests = SmsPackagePurchase::with(['school', 'package'])
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $requests
        ]);
    }

    /**
     * Approve and Activate the SMS Bundle.
     */
    public function approve(Request $request, $id)
    {
        return DB::transaction(function () use ($id, $request) {
            // Load with package to get validity_days
            $purchase = SmsPackagePurchase::with('package')->findOrFail($id);

            if ($purchase->status === 'approved') {
                return response()->json(['status' => 'error', 'message' => 'This bundle is already active'], 422);
            }

            // Force local timezone for Dhaka
            $activationDate = Carbon::now('Asia/Dhaka');
            $validityDays = (int)($purchase->package->validity_days ?? 0);

            // Calculate expiry based on the moment of approval using Dhaka time
            $expiryDate = $activationDate->copy()->addDays($validityDays);

            // 1. Update the Purchase Record
            $purchase->update([
                'status' => 'approved',
                'total_sms' => $purchase->total_sms,
                'available_sms' => $purchase->total_sms,
                'used_sms' => 0,
                'purchase_date' => $activationDate,
                'expiry_date' => $expiryDate,
                'admin_note' => $request->admin_note ?? 'Approved and activated by Admin'
            ]);

            // 2. Update the School's total master balance
            $school = School::findOrFail($purchase->school_id);
            $school->increment('sms_balance', $purchase->total_sms);

            return response()->json([
                'status' => 'success',
                'message' => "Bundle Activated! Valid until " . $expiryDate->format('j-F-Y'),
                'expiry_date' => $expiryDate->toDateTimeString(),
                'new_balance' => $school->sms_balance // Refreshed balance
            ]);
        });
    }

    /**
     * Reject the SMS Request.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'required|string|max:500'
        ]);

        $purchase = SmsPackagePurchase::findOrFail($id);

        if ($purchase->status === 'approved') {
            return response()->json(['status' => 'error', 'message' => 'Cannot reject an already approved bundle'], 422);
        }

        $purchase->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Request Rejected successfully'
        ]);
    }

    /**
     * Update Transaction ID or Notes (PUT).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'trx_id' => 'required|string',
            'admin_note' => 'nullable|string'
        ]);

        $purchase = SmsPackagePurchase::findOrFail($id);

        $purchase->update([
            'trx_id' => $request->trx_id,
            'admin_note' => $request->admin_note
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Request details updated'
        ]);
    }

    /**
     * Delete the record and cleanup all related balance data.
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            // Find the record
            $purchase = SmsPackagePurchase::findOrFail($id);

            /**
             * If the package was 'approved', we must subtract the 'available_sms' 
             * from the school's master balance before deleting the record.
             * This ensures the school doesn't keep credits that no longer exist in history.
             */
            if ($purchase->status === 'approved') {
                $school = School::find($purchase->school_id);
                if ($school) {
                    // Only deduct what was actually remaining in this specific bundle
                    $deductionAmount = $purchase->available_sms;

                    // Ensure we don't drop the balance below 0 unnecessarily, though 
                    // logic-wise this should match perfectly.
                    $newBalance = max(0, $school->sms_balance - $deductionAmount);

                    $school->update(['sms_balance' => $newBalance]);
                }
            }

            // Perform the hard delete
            $purchase->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase record deleted and school balance synchronized successfully.'
            ]);
        });
    }
}
