<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeacherProfileEditController extends Controller
{
    /**
     * Change teacher password.
     */
    public function changePassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Check if user exists to avoid errors
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        // Validation
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Current password is incorrect'], 422);
        }

        // Update password
        $hashedPassword = Hash::make($request->new_password);
        $user->password = $hashedPassword;
        $user->save();

        // Sync with teachers table
        // Added on 2026-07-11: Sync password to teachers table
        \App\Models\Teacher::where('id_number', $user->id_number)->update([
            'password' => $hashedPassword
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully!'
        ]);
    }
}
