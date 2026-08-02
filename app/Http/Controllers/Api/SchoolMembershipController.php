<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolMembership;
use App\Models\School;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolMembershipController extends Controller
{
    /**
     * Helper to get the School ID for the current logged-in user
     */
    private function getSchoolId()
    {
        // Find the record in 'schools' table where user_id matches Auth::id()
        $school = School::where('user_id', Auth::id())->first();
        return $school ? $school->id : null;
    }

    /**
     * Get all memberships for the authenticated school.
     */
    public function index(Request $request)
    {
        $schoolId = $this->getSchoolId();

        if (!$schoolId) {
            return response()->json(['message' => 'School profile not found for this user.'], 404);
        }

        $search = $request->search;

        $memberships = SchoolMembership::where('school_id', $schoolId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('member_no', 'like', "%{$search}%") // Added member_no to search
                        ->orWhere('mobile_number', 'like', "%{$search}%")
                        ->orWhere('income_source', 'like', "%{$search}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return response()->json($memberships);
    }

    /**
     * Store a new membership.
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_no'     => 'required|string|max:50', // Added member_no validation
            'name'          => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'amount'        => 'required|numeric',
        ]);

        $schoolId = $this->getSchoolId();

        if (!$schoolId) {
            return response()->json(['message' => 'Cannot save: School profile missing.'], 403);
        }

        $membership = DB::transaction(function () use ($schoolId, $request) {
            $membership = SchoolMembership::create([
                'school_id'     => $schoolId,
                'member_no'     => $request->member_no, // Added member_no
                'name'          => $request->name,
                'address'       => $request->address,
                'mobile_number' => $request->mobile_number,
                'income_source' => $request->income_source,
                'amount'        => $request->amount,
            ]);

            // Cash In to the internal System Cash Balance (immutable ledger)
            app(AccountService::class)->cashIn(
                $schoolId,
                (float) $request->amount,
                'Other Receipts',
                $membership->id,
                [
                    'remarks' => 'Membership - ' . ($request->income_source ?? '') . ' | ' . $request->name,
                ]
            );

            return $membership;
        });

        return response()->json([
            'message' => 'Member created successfully',
            'data'    => $membership
        ], 201);
    }

    /**
     * Update an existing membership.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'member_no'     => 'required|string|max:50', // Added member_no validation
            'name'          => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'amount'        => 'required|numeric',
        ]);

        $schoolId = $this->getSchoolId();

        $membership = SchoolMembership::where('id', $id)
            ->where('school_id', $schoolId)
            ->firstOrFail();

        $membership = DB::transaction(function () use ($membership, $request) {
            $oldAmount = (float) $membership->amount;

            $membership->update([
                'member_no'     => $request->member_no, // Added member_no
                'name'          => $request->name,
                'address'       => $request->address,
                'mobile_number' => $request->mobile_number,
                'income_source' => $request->income_source,
                'amount'        => $request->amount,
            ]);

            $newAmount = (float) $request->amount;
            if (abs($newAmount - $oldAmount) > 0.0001) {
                $service = app(AccountService::class);
                $service->cashOut($membership->school_id, $oldAmount, 'Other Receipts Adjustment', $membership->id, [
                    'remarks' => 'Reversal of membership #' . $membership->id
                        . ' (amount adjusted ' . number_format($oldAmount, 2) . ' -> ' . number_format($newAmount, 2) . ')',
                ]);
                $service->cashIn($membership->school_id, $newAmount, 'Other Receipts', $membership->id, [
                    'remarks' => 'Membership - ' . ($request->income_source ?? '') . ' | ' . $request->name . ' (adjusted)',
                ]);
            }
        });

        return response()->json([
            'message' => 'Member updated successfully',
            'data'    => $membership
        ]);
    }

    /**
     * Delete a membership.
     */
    public function destroy($id)
    {
        $schoolId = $this->getSchoolId();

        $membership = SchoolMembership::where('id', $id)
            ->where('school_id', $schoolId)
            ->firstOrFail();

        DB::transaction(function () use ($membership) {
            $amount = (float) $membership->amount;
            $membershipId = $membership->id;
            $membership->delete();

            if ($amount > 0) {
                app(AccountService::class)->cashOut($membership->school_id, $amount, 'Other Receipts Adjustment', $membershipId, [
                    'remarks' => 'Reversal of deleted membership #' . $membershipId,
                ]);
            }
        });

        return response()->json([
            'message' => 'Member deleted successfully'
        ]);
    }
}
