<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 

class AdminProfileEditController extends Controller
{
    /**
     * Updated the admin profile information.
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Check if user exists to avoid the save() error on null
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'password' => 'nullable|min:6|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Update Basic Info
        $user->name = $request->name;
        $user->mobile = $request->mobile;

        // Update Password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Handle Image Upload
        if ($request->hasFile('profile_image')) {
            // Delete the old image from storage if it exists
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // Store the new image in the 'profile_images' folder within the 'public' disk
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        // The save() method is part of Eloquent Model
        $user->save();

        // Prepare the image URL for the frontend response
        $imageUrl = $user->profile_image
            ? asset('storage/' . $user->profile_image)
            : asset('images/avatar.svg');

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'new_img' => $imageUrl,
            'user' => [
                'name' => $user->name,
                'mobile' => $user->mobile
            ]
        ]);
    }
}
