<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SmsPackage;
use App\Models\SmsPackagePurchase;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SchoolSmsPackageController extends Controller
{
    /**
     * Helper to get current school record
     */
    private function getSchool()
    {
        return School::where('user_id', Auth::id())->first();
    }

    /**
     * Helper to get current school ID
     */
    private function getSchoolId()
    {
        $school = $this->getSchool();
        return $school ? $school->id : null;
    }

    /**
     * List all active packages
     */
    public function index()
    {
        try {
            $packages = SmsPackage::whereIn('status', ['active', '1', 1, 'Active'])
                ->orderBy('sale_price', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $packages
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get real-time SMS balance from the master school record
     */
    public function getBalance()
    {
        $school = $this->getSchool();

        if (!$school) {
            return response()->json(['status' => 'error', 'message' => 'School not found'], 404);
        }

        // We return the direct balance from the school table to ensure 
        // it matches what the SMS sending service sees.
        return response()->json([
            'status' => 'success',
            'balance' => $school->sms_balance ?? 0
        ]);
    }

    /**
     * Get purchase history for the school
     */
    public function purchaseHistory()
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) return response()->json(['status' => 'error', 'message' => 'School not found'], 404);

        $history = SmsPackagePurchase::with('package:id,name')
            ->where('school_id', $schoolId)
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $history
        ]);
    }

    /**
     * Create a new purchase/activation request
     */
    public function purchase(Request $request)
    {
        $request->validate([
            'sms_package_id' => 'required|exists:sms_packages,id',
            'payment_method' => 'required|string'
        ]);

        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return response()->json(['status' => 'error', 'message' => 'School record not found.'], 404);
        }

        $package = SmsPackage::findOrFail($request->sms_package_id);

        try {
            // Force the timezone to Dhaka to match your local date
            $today = Carbon::now('Asia/Dhaka');

            $validityDays = (int)($package->validity_days ?? 0);
            $profit = $package->sale_price - $package->purchase_price;

            $purchase = SmsPackagePurchase::create([
                'trx_id'         => 'SMS-' . strtoupper(Str::random(10)),
                'school_id'      => $schoolId,
                'sms_package_id' => $package->id,
                'total_sms'      => $package->sms_quantity,
                'used_sms'       => 0,
                'available_sms'  => 0, // Starts at 0 until admin approves the payment
                'purchase_price' => $package->purchase_price,
                'sale_price'     => $package->sale_price,
                'profit'         => $profit,
                'status'         => 'pending',
                'payment_method' => $request->payment_method,
                'purchase_date'  => $today,
                'expiry_date'    => $today->copy()->addDays($validityDays),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Request submitted! Please complete payment of ৳' . number_format($package->sale_price, 2),
                'trx_id'  => $purchase->trx_id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create request: ' . $e->getMessage()
            ], 500);
        }
    }
}
