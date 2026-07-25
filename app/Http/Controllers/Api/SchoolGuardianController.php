<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdmissionStudent;
use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SchoolGuardianController extends Controller
{
    public function index(Request $request)
    {
        // Eager load relationships to get names/years instead of foreign IDs
        $query = AdmissionStudent::with([
            'guardian',
            'schoolClass',
            'schoolGroup',
            'schoolSection',
            'schoolSession' // Eager load the session relationship
        ])
            ->where('school_id', Auth::id())
            // Modified on 2026-07-07: Allow both Active and approved statuses
            ->whereIn('status', ['Active', 'approved']);

        if ($request->class) {
            $query->where('class', $request->class);
        }

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('student_name', 'like', "%$s%")
                    ->orWhere('student_id_number', 'like', "%$s%")
                    ->orWhereHas('guardian', function ($gq) use ($s) {
                        $gq->where('name', 'like', "%$s%")
                            ->orWhere('mobile', 'like', "%$s%");
                    });
            });
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function show($id)
    {
        // Added status check for security
        return AdmissionStudent::with('guardian')
            ->where('school_id', Auth::id())
            // Modified on 2026-07-07: Allow both Active and approved statuses
            ->whereIn('status', ['Active', 'approved'])
            ->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        // Added status check to prevent updating pending students here
        $student = AdmissionStudent::where('school_id', Auth::id())
            // Modified on 2026-07-07: Allow both Active and approved statuses
            ->whereIn('status', ['Active', 'approved'])
            ->findOrFail($id);

        // 1. Handle Image Upload
        if ($request->hasFile('image')) {
            if ($student->image) {
                Storage::disk('public')->delete($student->image);
            }
            $imagePath = $request->file('image')->store('students', 'public');
            $student->image = $imagePath;
        }

        // 2. Update Student Info
        $student->update([
            'student_name'      => $request->student_name,
            'father_name'       => $request->father_name,
            'mother_name'       => $request->mother_name,
            'student_id_number' => $request->student_id_number,
            'class'             => $request->class,
            'session'           => $request->session,
        ]);

        // 3. Update Guardian Info
        if ($student->guardian_id) {
            Guardian::where('id', $student->guardian_id)->update([
                'name'     => $request->guardian_name,
                'mobile'   => $request->guardian_mobile,
                'relation' => $request->relation,
            ]);
        }

        return response()->json(['message' => 'Profile Updated Successfully']);
    }

    public function destroy($id)
    {
        // Added status check
        $student = AdmissionStudent::where('school_id', Auth::id())
            // Modified on 2026-07-07: Allow both Active and approved statuses
            ->whereIn('status', ['Active', 'approved'])
            ->findOrFail($id);

        if ($student->image) {
            Storage::disk('public')->delete($student->image);
        }

        $student->delete();
        return response()->json(['message' => 'Deleted Successfully']);
    }
}