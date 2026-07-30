<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolSubscription;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SchoolSubscriptionController extends Controller
{
    /**
     * Get current active subscription
     */
    public function current()
    {
        $school = Auth::user()->school;

        if (!$school) {
            return response()->json(['subscription' => null]);
        }

        $subscription = SchoolSubscription::with('package')
            ->where('school_id', $school->id)
            ->latest('expiry_date')
            ->first();

        if (!$subscription) {
            return response()->json(['subscription' => null]);
        }

        $today = Carbon::today();
        $daysRemaining = $today->diffInDays(Carbon::parse($subscription->expiry_date), false);

        return response()->json([
            'subscription' => [
                'id' => $subscription->id,
                'package_name' => $subscription->package->package_type,
                'start_date' => Carbon::parse($subscription->start_date)->format('j-F-Y'),
                'start_date_raw' => Carbon::parse($subscription->start_date)->format('Y-m-d'),
                'expires_at' => Carbon::parse($subscription->expiry_date)->format('j-F-Y'),
                'days_remaining' => max($daysRemaining, 0),
                'status' => $subscription->status,
            ]
        ]);
    }

    /**
     * Renew subscription
     */
    public function renew(Request $request, $id)
    {
        $request->validate([
            'duration' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
        ]);

        $subscription = SchoolSubscription::findOrFail($id);

        $duration = (int) $request->duration;
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::parse($subscription->expiry_date);

        $subscription->start_date = $startDate;
        $subscription->expiry_date = $startDate->copy()->addMonths($duration);
        $subscription->duration_months += $duration;
        $subscription->status = 'active';
        $subscription->save();

        return response()->json([
            'message' => 'Subscription renewed successfully',
            'start_date' => $subscription->start_date->format('Y-m-d'),
            'expiry_date' => $subscription->expiry_date->format('Y-m-d'),
        ]);
    }

    /**
     * Fetch all active packages for change plan
     */
    public function packagesFetch()
    {
        $packages = Package::where('is_active', 1)->get(['id', 'package_type', 'student_limit', 'teacher_limit', 'per_student_price']);

        return response()->json([
            'packages' => $packages
        ]);
    }

    /**
     * Change subscription plan
     */
    public function change(Request $request, $id)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'duration' => 'required|integer|min:1',
            'start_date' => 'required|date',
        ]);

        $subscription = SchoolSubscription::findOrFail($id);
        $package = Package::findOrFail($request->package_id);

        $duration = (int) $request->duration;
        $startDate = Carbon::parse($request->start_date);

        // Calculate price
        $originalPrice = $package->per_student_price * $package->student_limit * $duration;
        $discountPercent = $package->annual_discount_percent ?? 0;
        $finalPrice = $originalPrice - ($originalPrice * $discountPercent / 100);

        // Update subscription
        $subscription->package_id = $package->id;
        $subscription->start_date = $startDate;
        $subscription->expiry_date = $startDate->copy()->addMonths($duration);
        $subscription->duration_months = $duration;
        $subscription->original_price = $originalPrice;
        $subscription->discount_percent = $discountPercent;
        $subscription->final_price = $finalPrice;
        $subscription->status = 'active';
        $subscription->save();

        return response()->json([
            'message' => 'Subscription plan changed successfully',
            'subscription' => [
                'package_name' => $package->package_type,
                'start_date' => $subscription->start_date->format('Y-m-d'),
                'expiry_date' => $subscription->expiry_date->format('Y-m-d'),
                'duration_months' => $subscription->duration_months,
                'final_price' => $subscription->final_price,
            ]
        ]);
    }
}
