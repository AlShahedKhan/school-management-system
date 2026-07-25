<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;
use App\Models\Package;
use App\Models\SchoolSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SchoolController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'school_name' => 'required',
            'division' => 'required',
            'district' => 'required',
            'upazila' => 'required',
            'village' => 'required',

            'eiin_number' => 'required',
            'mobile' => 'required|unique:users,mobile',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'logo' => 'required|image',

            'package_id' => 'required|exists:packages,id',
            'duration_months' => 'required|integer|min:1',
            'start_date' => 'required|date',
        ]);

        DB::beginTransaction();

        try {

            // ================= FIXED ID GENERATION =================
            // Specifically look for the last user whose role is 'school'
            $lastSchoolUser = User::where('role', 'school')
                ->orderBy('id', 'desc')
                ->first();

            if (!$lastSchoolUser) {
                // If no school exists yet, start at 2401
                $nextNumber = 2401;
            } else {
                // Increment from the last school's ID_NUMBER
                $lastIdNum = (int) $lastSchoolUser->id_number;

                // Safety check: if for some reason the last ID was below 2401, reset to 2401
                $nextNumber = ($lastIdNum < 2401) ? 2401 : $lastIdNum + 1;
            }

            // Generate the 8-digit padded ID (e.g., 00002401)
            $generatedId = str_pad($nextNumber, 8, '0', STR_PAD_LEFT);

            // ================= CREATE USER =================
            $user = User::create([
                'role' => 'school',
                'name' => $request->school_name,
                'school_name' => $request->school_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'id_number' => $generatedId,
                'password' => Hash::make($request->password),
            ]);

            // ================= UPLOAD LOGO =================
            $logoPath = $request->file('logo')->store('school_logos', 'public');

            // ================= FETCH PACKAGE DATA =================
            // We fetch this early to get the limits for school and subscription tables
            $package = Package::findOrFail($request->package_id);

            // ================= CREATE SCHOOL =================
            $school = School::create([
                'user_id' => $user->id,
                'school_name' => $request->school_name,
                'division' => $request->division,
                'district' => $request->district,
                'upazila' => $request->upazila,
                'village' => $request->village,
                'id_number' => $generatedId,
                'eiin_number' => $request->eiin_number,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'logo' => $logoPath,
                'sms_balance' => $package->sms_limit ?? 0,
                'approval_status' => 'pending',
            ]);

            // ================= CREATE SUBSCRIPTION =================
            $duration = (int) $request->duration_months;
            $startDate = Carbon::parse($request->start_date);
            $expiryDate = $startDate->copy()->addMonths($duration);

            // ---- Total Price Calculation ----
            $totalPrice = $package->per_student_price
                * $package->student_limit
                * $duration;

            // ---- Default Discount Values ----
            $discountPercent = 0;
            $finalPrice = $totalPrice; // Default if no discount applies

            // ---- Apply Discount ONLY if 12 months ----
            if ($duration === 12 && $package->annual_discount_percent > 0) {
                $discountPercent = $package->annual_discount_percent;
                $finalPrice = $totalPrice - ($totalPrice * $discountPercent / 100);
            }

            SchoolSubscription::create([
                'school_id' => $school->id,
                'package_id' => $package->id,
                'duration_months' => $duration,

                'original_price' => $totalPrice,
                'discount_percent' => $discountPercent,
                'final_price' => $finalPrice,

                'start_date' => $startDate,
                'expiry_date' => $expiryDate,
                'status' => 'pending',

                // New logic: Store package snapshots and default upgrade type
                'upgrade_type' => 'subscription',
                'student_limit' => $package->student_limit,
                'teacher_limit' => $package->teacher_limit,
                'per_student_price' => $package->per_student_price,
                // These remain null on registration as they belong to 'sale' or 'contract' types
                'sale_date' => null,
                'contract_close_date' => null,
            ]);

            // ================= SEND SMS =================
            $this->sendRegistrationSMS($user, $generatedId);

            DB::commit();

            return response()->json([
                'redirect' => '/school/approval-status?id=' . $generatedId
                    . '&name=' . urlencode($request->school_name)
                    . '&password=' . urlencode($request->password), // <- raw password added
                'message' => 'Registration successful.'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error("Registration Error: " . $e->getMessage());

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * SMS Logic for School Registration (English)
     */
    private function sendRegistrationSMS($user, $generatedId)
    {
        try {
            // Standardize Phone Number
            $phone = preg_replace('/[^0-9]/', '', $user->mobile);
            if (str_starts_with($phone, '0')) {
                $phone = '880' . substr($phone, 1);
            }

            // English Message Format
            $message = "Registration Successful!\n" .
                "Institution: {$user->school_name}\n" .
                "Your ID: {$generatedId}\n" .
                "Status: Pending Approval\n" .
                "Thank you for being with Astha Academic.";

            $baseUrl = rtrim(env('SMS_BASE_URL'), '/');

            // Dispatch SMS
            Http::withoutVerifying()
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(20)
                ->post("{$baseUrl}/api/SmsSending/SMS", [
                    "UserName" => (string) env('SMS_USERNAME'),
                    "Apikey" => (string) env('SMS_API_KEY'),
                    "MobileNumber" => (string) $phone,
                    "CampaignId" => "null",
                    "SenderName" => (string) env('SMS_SENDER_ID'),
                    "TransactionType" => "T",
                    "Message" => (string) $message
                ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Registration SMS Error: " . $e->getMessage());
            return false;
        }
    }
}
