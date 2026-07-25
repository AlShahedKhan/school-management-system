<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\School;
use App\Services\SmsService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Normalize mobile number to standard format
     */
    private function formatMobile($mobile)
    {
        return preg_replace('/[^0-9]/', '', $mobile);
    }

    /**
     * STEP 1: SEND OTP
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required'
        ]);

        $formattedMobile = $this->formatMobile($request->mobile);

        // Find user and their associated school
        $user = User::where('mobile', $formattedMobile)->first();
        if (!$user) {
            return response()->json(['message' => 'No account found with this mobile number.'], 404);
        }

        $school = School::where('user_id', $user->id)->first();
        if (!$school) {
            return response()->json(['message' => 'Associated school profile not found.'], 404);
        }

        // Clean up expired OTPs (older than 24 hours)
        DB::table('password_resets')
            ->where('mobile', $formattedMobile)
            ->where('created_at', '<', now()->subDay())
            ->delete();

        // Check OTP limit in last 24 hours
        $otpCount = DB::table('password_resets')
            ->where('mobile', $formattedMobile)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        if ($otpCount >= 3) {
            return response()->json([
                'message' => 'You have reached the maximum OTP requests (3) for today. Try again after 24 hours.'
            ], 429);
        }

        // Generate 4-digit OTP
        $otp = rand(1000, 9999);

        // Store OTP
        DB::table('password_resets')->insert([
            'mobile'     => $formattedMobile,
            'otp'        => $otp,
            'created_at' => now()
        ]);

        // Send OTP via SmsService (handles balance deduction)
        $smsResponse = $this->smsService->sendSms($school, $formattedMobile, "Your password reset code is: $otp");

        if (!$smsResponse['success']) {
            return response()->json([
                'message' => $smsResponse['message']
            ], 400);
        }

        return response()->json(['message' => 'OTP sent successfully.']);
    }

    /**
     * STEP 2: VERIFY OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'otp'    => 'required'
        ]);

        $formattedMobile = $this->formatMobile($request->mobile);

        $reset = DB::table('password_resets')
            ->where('mobile', $formattedMobile)
            ->where('otp', $request->otp)
            ->orderByDesc('created_at')
            ->first();

        if (!$reset) {
            return response()->json(['message' => 'Invalid OTP.'], 422);
        }

        if (Carbon::parse($reset->created_at)->addMinutes(2)->isPast()) {
            return response()->json(['message' => 'OTP has expired.'], 422);
        }

        return response()->json(['message' => 'OTP verified successfully.']);
    }

    /**
     * STEP 3: RESET PASSWORD
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'mobile'   => 'required',
            'otp'      => 'required',
            'password' => 'required|confirmed|min:6'
        ]);

        $formattedMobile = $this->formatMobile($request->mobile);

        $reset = DB::table('password_resets')
            ->where('mobile', $formattedMobile)
            ->where('otp', $request->otp)
            ->orderByDesc('created_at')
            ->first();

        if (!$reset) {
            return response()->json(['message' => 'Unauthorized request.'], 422);
        }

        if (Carbon::parse($reset->created_at)->addMinutes(2)->isPast()) {
            return response()->json(['message' => 'OTP has expired.'], 422);
        }

        $user = User::where('mobile', $formattedMobile)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')
            ->where('mobile', $formattedMobile)
            ->delete();

        return response()->json(['message' => 'Password reset successful.']);
    }
}
