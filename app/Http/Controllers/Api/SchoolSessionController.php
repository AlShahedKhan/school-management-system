<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolSession;
use App\Http\Requests\SchoolSessionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolSessionController extends Controller
{
    /**
     * Helper to get the school associated with the authenticated user.
     */
    private function getSchool($user)
    {
        return School::where('user_id', $user->id)->first();
    }

    public function index(Request $request)
    {
        $school = $this->getSchool($request->user());
        if (!$school) return response()->json(['data' => [], 'total' => 0]);

        $query = SchoolSession::with(['schoolClass', 'schoolGroup', 'schoolSection'])
            ->where('school_id', $school->id);

        // Filter by class_id
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by group_id
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        // Filter by section_id
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        // Filter by session_year
        if ($request->filled('session_year')) {
            $query->where('session_year', $request->session_year);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('session_year', 'like', "%{$request->search}%")
                    ->orWhereHas('schoolClass', function ($sq) use ($request) {
                        $sq->where('class_name', 'like', "%{$request->search}%");
                    });
            });
        }

        return response()->json($query->latest()->paginate(10));
    }

    public function store(SchoolSessionRequest $request)
    {
        try {
            $school = $this->getSchool($request->user());
            if (!$school) return response()->json(['message' => 'School profile not found.'], 404);

            $session = DB::transaction(function () use ($school, $request) {
                return SchoolSession::create([
                    'school_id'      => $school->id,
                    'class_id'       => $request->validated()['class_id'],
                    'group_id'       => $request->validated()['group_id'] ?? null,
                    'section_id'     => $request->validated()['section_id'] ?? null,
                    'session_year'   => $request->validated()['session_year'],
                    'start_date'     => $request->validated()['start_date'],
                    'end_date'       => $request->validated()['end_date'],
                    'total_days'     => $request->validated()['total_days'],
                    'remaining_days' => $request->validated()['remaining_days'],
                ]);
            });

            return response()->json(['message' => 'Session created successfully', 'data' => $session]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create session.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $session = SchoolSession::where('school_id', $school->id)->findOrFail($id);
        return response()->json($session);
    }

    public function update(SchoolSessionRequest $request, $id)
    {
        try {
            $school = $this->getSchool($request->user());

            DB::transaction(function () use ($school, $id, $request) {
                $session = SchoolSession::where('school_id', $school->id)->findOrFail($id);

                $session->update([
                    'class_id'       => $request->validated()['class_id'],
                    'group_id'       => $request->validated()['group_id'] ?? null,
                    'section_id'     => $request->validated()['section_id'] ?? null,
                    'session_year'   => $request->validated()['session_year'],
                    'start_date'     => $request->validated()['start_date'],
                    'end_date'       => $request->validated()['end_date'],
                    'total_days'     => $request->validated()['total_days'],
                    'remaining_days' => $request->validated()['remaining_days'],
                ]);
            });

            return response()->json(['message' => 'Session updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update session.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $school = $this->getSchool($request->user());

            DB::transaction(function () use ($school, $id) {
                SchoolSession::where('school_id', $school->id)->findOrFail($id)->delete();
            });

            return response()->json(['message' => 'Session deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete session.', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function getDates(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $session = SchoolSession::where('school_id', $school->id)->findOrFail($id);
        return response()->json([
            'start_date' => $session->start_date,
            'end_date'   => $session->end_date,
        ]);
    }
    
        //School Session Filter
    public function sessionFilter(Request $request)
    {
        $school = $this->getSchool($request->user());

        if (!$school) {
            return response()->json([
                'message' => 'School profile not found.',
                'data' => []
            ], 404);
        }

        $query = SchoolSession::with([
                'schoolClass',
                'schoolGroup',
                'schoolSection'
            ])
            ->where('school_id', $school->id);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('session_year', 'like', "%{$search}%")
                ->orWhereHas('schoolClass', function ($sq) use ($search) {
                    $sq->where('class_name', 'like', "%{$search}%");
                })
                ->orWhereHas('schoolGroup', function ($sg) use ($search) {
                    $sg->where('group_name', 'like', "%{$search}%");
                })
                ->orWhereHas('schoolSection', function ($ss) use ($search) {
                    $ss->where('section_name', 'like', "%{$search}%");
                });
            });
        }

        $sessions = $query->latest()->paginate(10);

        if ($sessions->total() == 0) {
            return response()->json([
                'message' => 'No session found for "' . $request->search . '"',
                'data' => [],
                'total' => 0
            ]);
        }

        return response()->json($sessions);
    } 
}
