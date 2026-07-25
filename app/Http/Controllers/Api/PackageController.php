<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $query = Package::query();

        if ($request->search) {
            $query->where('package_type', 'like', "%{$request->search}%");
        }

        if ($request->type) {
            $query->where('package_type', $request->type);
        }

        return response()->json(
            $query->latest()->paginate(10)
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'package_type' => 'required',
            'student_limit' => 'required|integer',
            'teacher_limit' => 'required|integer',
            'per_student_price' => 'required|numeric',
        ]);

        $total = $request->student_limit * $request->per_student_price;

        $afterDiscount = $total - ($total * ($request->annual_discount_percent / 100));

        $package = Package::create([
            'package_type' => $request->package_type,
            'student_limit' => $request->student_limit,
            'teacher_limit' => $request->teacher_limit,
            'free_trial_days' => $request->free_trial_days ?? 0,
            'per_student_price' => $request->per_student_price,
            'total_payable' => $total,
            'annual_discount_percent' => $request->annual_discount_percent ?? 0,
            'after_discount' => $afterDiscount,
            'sms_limit' => $request->sms_limit ?? 0,
        ]);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return response()->json(Package::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $package = Package::findOrFail($id);

        $total = $request->student_limit * $request->per_student_price;

        $afterDiscount = $total - ($total * ($request->annual_discount_percent / 100));

        $package->update([
            'package_type' => $request->package_type,
            'student_limit' => $request->student_limit,
            'teacher_limit' => $request->teacher_limit,
            'free_trial_days' => $request->free_trial_days ?? 0,
            'per_student_price' => $request->per_student_price,
            'total_payable' => $total,
            'annual_discount_percent' => $request->annual_discount_percent ?? 0,
            'after_discount' => $afterDiscount,
            'sms_limit' => $request->sms_limit ?? 0,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Package::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }
}
