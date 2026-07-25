<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Principal;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PrincipalController extends Controller
{
    public function show()
    {
        $school = School::where('user_id', Auth::id())->first();
        if (!$school) {
            return response()->json(['success' => false, 'message' => 'School not found.'], 404);
        }

        $principal = Principal::where('school_id', $school->id)->first();

        if ($principal) {
            $this->ensureIdNumber($principal, $school);
        }

        return response()->json([
            'success' => true,
            'data' => $principal
                ? array_merge($principal->toArray(), ['has_pin' => filled($principal->pin)])
                : null,
        ]);
    }

    public function update(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();
        if (!$school) {
            return response()->json(['success' => false, 'message' => 'School not found.'], 404);
        }

        $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'pin'          => ['nullable', 'regex:/^\d{8}$/'],
            'designation'  => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'is_active'    => 'nullable|boolean',
            'photo'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'signature'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $existingPrincipal = Principal::where('school_id', $school->id)->first();

        $principal = Principal::updateOrCreate(
            ['school_id' => $school->id],
            [
                'name'         => $request->name,
                'phone'        => $request->phone,
                'email'        => $request->email,
                'designation'  => $request->designation,
                'joining_date' => $request->joining_date,
                'is_active'    => $request->has('is_active')
                    ? $request->boolean('is_active')
                    : ($existingPrincipal?->is_active ?? true),
            ]
        );

        $this->ensureIdNumber($principal, $school);

        if ($request->filled('pin')) {
            $principal->pin = Hash::make($request->string('pin')->toString());
        } elseif (blank($principal->pin)) {
            $principal->pin = Hash::make('00000000');
        }

        if ($request->hasFile('photo')) {
            if ($principal->photo) {
                Storage::disk('public')->delete($principal->photo);
            }
            $principal->photo = $request->file('photo')->store('principals/photos', 'public');
        }

        if ($request->hasFile('signature')) {
            if ($principal->signature) {
                Storage::disk('public')->delete($principal->signature);
            }
            $principal->signature = $request->file('signature')->store('principals/signatures', 'public');
        }

        $principal->save();

        return response()->json([
            'success' => true,
            'message' => 'Principal information updated successfully!',
            'data' => $principal
        ]);
    }

    private function ensureIdNumber(Principal $principal, School $school): void
    {
        if (filled($principal->id_number)) {
            return;
        }

        $principal->id_number = sprintf('PRN-%04d-%04d', $school->id, $principal->id);
        $principal->save();
    }
}
