<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Donate;
use Illuminate\Support\Facades\DB;

class SchoolDonateController extends Controller
{

    private function getSchool($user)
    {
        return DB::table('schools')
            ->where('user_id', $user->id)
            ->first();
    }

    public function index(Request $request)
    {
        $school = $this->getSchool($request->user());
        if (!$school) {
            return response()->json([
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'total' => 0,
            ]);
        }
        $query = Donate::where('school_id', $school->id);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('donate_no', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('donate_reason', 'like', "%{$search}%");
            });
        }
        if ($request->filled('month')) {
            $query->whereMonth('donate_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('donate_date', $request->year);
        }
        return response()->json(
            $query->latest('donate_date')->paginate(10)
        );
    }

    public function store(Request $request)
    {
        $school = $this->getSchool($request->user());
        if (!$school) {
            return response()->json([
                'message' => 'School profile not found.'
            ], 404);
        }
        $request->validate([
            'donate_date'   => 'required|date',
            'name'          => 'required|string|max:150',
            'mobile_number' => 'required|string|max:20',
            'location'      => 'nullable|string|max:255',
            'donate_reason' => 'nullable|string',
            'amount'        => 'required|numeric|min:0',
        ]);
        $donate = Donate::create([
            'school_id'     => $school->id,
            'donate_date'   => $request->donate_date,
            'name'          => $request->name,
            'mobile_number' => $request->mobile_number,
            'location'      => $request->location,
            'donate_reason' => $request->donate_reason,
            'amount'        => $request->amount,
        ]);
        return response()->json([
            'message' => 'Donate recorded successfully.',
            'donate'  => $donate,
        ]);
    }
}
