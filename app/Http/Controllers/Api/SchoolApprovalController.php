<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\SchoolSubscription;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SchoolApprovalController extends Controller
{
    // Get all pending schools
    public function pendingSchools(Request $request)
    {
        $query = School::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('school_name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('mobile', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->date_filter === 'latest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($request->date_filter === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $schools = $query->get()->map(function ($school) {
            $subscription = $school->subscriptions()->latest()->first();
            $package = $subscription?->package; // relation to packages
            return [
                'id' => $school->id,
                'school_name' => $school->school_name,
                'division' => $school->division,
                'district' => $school->district,
                'upazila' => $school->upazila,
                'village' => $school->village,
                'email' => $school->email,
                'mobile' => $school->mobile,
                'eiin_number' => $school->eiin_number,
                'logo' => $school->logo,
                'approval_status' => $school->approval_status,
                'subscription_status' => $subscription?->status,
                'package_name' => $package?->package_type,
                'total_payable' => $package?->total_payable,
            ];
        });

        return response()->json($schools);
    }

    // Update approval status
    public function updateApprovalStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        DB::beginTransaction();
        try {
            $school = School::findOrFail($id);
            $school->approval_status = $request->status;
            $school->approved_at = $request->status === 'approved' ? Carbon::now() : null;
            $school->save();

            // Update subscription status
            $subscriptionStatus = $request->status === 'approved' ? 'active' : 'cancelled';
            SchoolSubscription::where('school_id', $school->id)
                ->update(['status' => $subscriptionStatus]);

            DB::commit();
            return response()->json(['message' => 'School status updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete school
    public function deleteSchool($id)
    {
        DB::beginTransaction();
        try {
            $school = School::findOrFail($id);
            SchoolSubscription::where('school_id', $school->id)->delete();
            $school->delete();
            DB::commit();
            return response()->json(['message' => 'School deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete school',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
