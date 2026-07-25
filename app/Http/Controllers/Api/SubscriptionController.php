<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolSubscription;
use App\Models\Package;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    // List all approved schools with subscriptions
    public function index(Request $request)
    {
        $query = SchoolSubscription::with(['school', 'package'])
            ->whereHas('school', fn($q) => $q->where('approval_status', 'approved'));

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('school', function ($q) use ($search) {
                $q->where('school_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        }

        // Date filter
        if ($request->date_filter === 'latest') $query->orderBy('created_at', 'desc');
        elseif ($request->date_filter === 'oldest') $query->orderBy('created_at', 'asc');
        else $query->orderBy('id', 'desc');

        $subscriptions = $query->get()->map(function ($sub) {
            return [
                'id' => $sub->id,
                'school_name' => $sub->school->school_name ?? 'N/A',
                'logo' => $sub->school->logo ?? '',
                'division' => $sub->school->division ?? '',
                'district' => $sub->school->district ?? '',
                'upazila' => $sub->school->upazila ?? '',
                'email' => $sub->school->email ?? '',
                'mobile' => $sub->school->mobile ?? '',
                'package_type' => $sub->package->package_type ?? '-',
                'package_id' => $sub->package_id,
                'duration_months' => $sub->duration_months,
                'original_price' => $sub->original_price,
                'discount_percent' => $sub->discount_percent,
                'final_price' => $sub->final_price,
                'start_date' => $sub->start_date,
                'expiry_date' => $sub->expiry_date,
                'status' => $sub->status,
                'upgrade_type' => $sub->upgrade_type,
                'student_limit' => $sub->student_limit,
                'teacher_limit' => $sub->teacher_limit,
                'per_student_price' => $sub->per_student_price,
                'sale_date' => $sub->sale_date,
                'contract_close_date' => $sub->contract_close_date,
                'total_payable' => $sub->final_price,
            ];
        });

        return response()->json($subscriptions);
    }

    // Update subscription status
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:active,cancelled']);
        $sub = SchoolSubscription::findOrFail($id);
        $sub->status = $request->status;
        $sub->save();

        return response()->json(['message' => 'Status updated']);
    }

    // Delete subscription
    public function destroy($id)
    {
        $sub = SchoolSubscription::findOrFail($id);
        $sub->delete();
        return response()->json(['message' => 'Subscription deleted']);
    }

    // Get subscription data for edit
    public function edit($id)
    {
        $sub = SchoolSubscription::findOrFail($id);
        return response()->json([
            'id' => $sub->id,
            'package_id' => $sub->package_id,
            'start_date' => $sub->start_date,
            'expiry_date' => $sub->expiry_date,
            'final_price' => $sub->final_price,
        ]);
    }

    // Update subscription after edit
    public function update(Request $request, $id)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'start_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:start_date',
            'final_price' => 'required|numeric|min:0',
        ]);

        $sub = SchoolSubscription::findOrFail($id);
        $sub->package_id = $request->package_id;
        $sub->start_date = $request->start_date;
        $sub->expiry_date = $request->expiry_date;
        $sub->final_price = $request->final_price;
        $sub->save();

        return response()->json(['message' => 'Subscription updated']);
    }

    // Fetch all active packages
    public function packages()
    {
        $packages = Package::where('is_active', 1)->get(['id', 'package_type']);
        return response()->json($packages);
    }

    /**
     * Upgrade existing subscription with custom parameters
     * Resets/Calculates fields based on the upgrade category selected.
     */
    public function upgrade(Request $request, $id)
    {
        $request->validate([
            'upgrade_category' => 'required|in:sale,contract,subscription',
            'student_limit' => 'required|integer|min:0',
            'teacher_limit' => 'required|integer|min:0',
            'sale_amount' => 'nullable|numeric|required_if:upgrade_category,sale',
            'sale_date' => 'nullable|date|required_if:upgrade_category,sale',
            'per_student' => 'nullable|numeric|required_if:upgrade_category,contract,subscription',
            'total_payable' => 'nullable|numeric|required_if:upgrade_category,contract,subscription',
            'contract_start' => 'nullable|date|required_if:upgrade_category,contract',
            'contract_close' => 'nullable|date|required_if:upgrade_category,contract',
        ]);

        $subscription = SchoolSubscription::findOrFail($id);
        $category = $request->upgrade_category;

        // ================= UNIVERSAL UPDATES =================
        $subscription->upgrade_type = $category;
        $subscription->student_limit = $request->student_limit;
        $subscription->teacher_limit = $request->teacher_limit;

        // Default resets (these will be specifically overwritten where needed below)
        $subscription->discount_percent = 0;
        $subscription->sale_date = null;
        $subscription->contract_close_date = null;

        // ================= CATEGORY SPECIFIC LOGIC =================
        if ($category === 'sale') {
            $subscription->duration_months = 0;
            $subscription->original_price = $request->sale_amount;
            $subscription->final_price = $request->sale_amount;
            $subscription->per_student_price = 0;
            $subscription->sale_date = $request->sale_date;
            $subscription->expiry_date = '2099-12-31';
        } elseif ($category === 'contract') {
            $start = Carbon::parse($request->contract_start);
            $close = Carbon::parse($request->contract_close);

            // Calculate duration in months (including partial months if desired, using diffInMonths)
            $subscription->duration_months = $start->diffInMonths($close);

            $subscription->per_student_price = $request->per_student;
            $subscription->original_price = $request->total_payable;
            $subscription->final_price = $request->total_payable;

            $subscription->start_date = $request->contract_start;
            $subscription->expiry_date = $request->contract_close;
            $subscription->contract_close_date = $request->contract_close;
        } elseif ($category === 'subscription') {
            // Limits and amounts increase, but dates and duration stay same
            $subscription->per_student_price = $request->per_student;
            $subscription->original_price = $request->total_payable;
            $subscription->final_price = $request->total_payable;

            // Note: start_date, expiry_date, and duration_months are NOT modified here
        }

        $subscription->save();

        return response()->json([
            'message' => 'Package upgraded successfully',
            'type' => $category,
            'final_price' => $subscription->final_price
        ]);
    }
}
