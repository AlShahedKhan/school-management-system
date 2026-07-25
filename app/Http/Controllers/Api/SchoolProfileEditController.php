<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolProfileEditController extends Controller
{
    /**
     * Update the school profile and the associated user account.
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // 1. Validate all editable fields
        $request->validate([
            'school_name'         => 'required|string|max:255',
            'mobile'              => 'required|string|max:20',
            'email'               => 'required|email|unique:users,email,' . $user->id,
            'division'            => 'nullable|string|max:100',
            'district'            => 'nullable|string|max:100',
            'upazila'             => 'nullable|string|max:100',
            'village'             => 'nullable|string|max:255',
            'eiin_number'         => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:100',
            'logo'               => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            // Password change validation
            'current_password'    => 'nullable|required_with:new_password|string',
            'new_password'        => 'nullable|string|min:8|confirmed',
        ]);

        $school = School::where('user_id', $user->id)->first();

        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'School record not found.'
            ], 404);
        }

        // Handle Password Change if requested
        if ($request->filled('new_password')) {
            if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password does not match.'
                ], 422);
            }
            $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
            $user->save();
        }

        // Start Transaction to ensure data integrity across both tables
        return DB::transaction(function () use ($request, $user, $school) {

            // 2. Update Users Table (Keep consistent with core school info)
            $user->update([
                'name'        => $request->input('school_name', $school->school_name),
                'school_name' => $request->input('school_name', $school->school_name),
                'mobile'      => $request->input('mobile', $school->mobile),
                'email'       => $request->input('email', $school->email),
            ]);

            // Preserve existing values when a focused form omits a field.
            $schoolData = [
                'school_name'         => $request->input('school_name', $school->school_name),
                'mobile'              => $request->input('mobile', $school->mobile),
                'email'               => $request->input('email', $school->email),
                'division'            => $request->input('division', $school->division),
                'district'            => $request->input('district', $school->district),
                'upazila'             => $request->input('upazila', $school->upazila),
                'village'             => $request->input('village', $school->village),
                'eiin_number'         => $request->input('eiin_number', $school->eiin_number),
                'registration_number' => $request->input('registration_number', $school->registration_number),
            ];

            // Handle Logo Upload (Delete old, store new)
            if ($request->hasFile('logo')) {
                if ($school->logo && Storage::disk('public')->exists($school->logo)) {
                    Storage::disk('public')->delete($school->logo);
                }

                $path = $request->file('logo')->store('school_logos', 'public');
                $schoolData['logo'] = $path;

                // Sync to user profile_image as well for topbar consistency
                $user->update(['profile_image' => $path]);
            }

            // 4. Update Schools Table
            $school->update($schoolData);

            // 5. Return Full Data for Frontend Persistence
            return response()->json([
                'success' => true,
                'message' => 'School profile updated successfully!',
                'data' => [
                    'school_name'         => $school->school_name,
                    'mobile'              => $school->mobile,
                    'email'               => $school->email,
                    'division'            => $school->division,
                    'district'            => $school->district,
                    'upazila'             => $school->upazila,
                    'village'             => $school->village,
                    'eiin_number'         => $school->eiin_number,
                    'registration_number' => $school->registration_number,
                    'logo_url'            => $school->logo ? asset('storage/' . $school->logo) : asset('images/default-school.png')
                ]
            ]);
        });
    }
}
