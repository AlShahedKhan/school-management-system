<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\User;
use App\Models\SchoolSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class RegisteredSchoolController extends Controller
{
    public function getRegisteredSchools(Request $request)
    {
        $query = School::where('approval_status', 'approved');
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('school_name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('mobile', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->date_filter === 'latest') $query->orderBy('created_at', 'desc');
        elseif ($request->date_filter === 'oldest') $query->orderBy('created_at', 'asc');
        else $query->orderBy('created_at', 'desc');

        $schools = $query->get()->map(function ($school) {
            $subscription = $school->subscriptions()->latest()->first();
            $package = $subscription?->package;
            return [
                'id' => $school->id,
                'school_name' => $school->school_name,
                'division' => $school->division,
                'district' => $school->district,
                'upazila' => $school->upazila,
                'village' => $school->village,
                'email' => $school->email,
                'mobile' => $school->mobile,
                'id_number' => $school->id_number,
                'eiin_number' => $school->eiin_number,
                'logo' => $school->logo,
                'subscription_status' => $subscription?->status,
                'package_type' => $package?->package_type,
                'duration_months' => $subscription?->duration_months,
                'total_payable' => $subscription?->final_price,
                'start_date' => $subscription?->start_date,
                'expiry_date' => $subscription?->expiry_date
            ];
        });

        return response()->json($schools);
    }

    public function getSchool($id)
    {
        $school = School::findOrFail($id);
        return response()->json($school);
    }

    public function updateSchool(Request $request, $id)
    {
        $school = School::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'school_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|string|max:20',
            'eiin_number' => 'nullable|string|max:50',
            'division' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'upazila' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
        ]);
        if ($validator->fails()) return response()->json(['message' => $validator->errors()->first()], 422);

        $school->update($request->only(['school_name', 'email', 'mobile', 'eiin_number', 'division', 'district', 'upazila', 'village']));
        return response()->json(['message' => 'School updated successfully']);
    }

    public function deleteSchool($id)
    {
        DB::beginTransaction();
        try {
            // 1. Find the school
            $school = School::findOrFail($id);

            // 2. Find the associated user
            $user = User::find($school->user_id);

            // 3. Delete logo from storage if it exists
            if ($school->logo) {
                Storage::disk('public')->delete($school->logo);
            }

            // 4. Delete related data explicitly (Optional if using Cascade Delete in migrations)
            // This ensures data is gone even if foreign keys aren't perfectly set
            SchoolSubscription::where('school_id', $school->id)->delete();

            // 5. Delete the school record
            $school->delete();

            // 6. Delete the user record (This removes them from the users table)
            if ($user) {
                $user->delete();
            }

            DB::commit();
            return response()->json(['message' => 'School and all associated account data deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to delete school', 'error' => $e->getMessage()], 500);
        }
    }
}
