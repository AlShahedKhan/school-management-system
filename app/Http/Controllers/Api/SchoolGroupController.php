<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolGroup;
use App\Http\Requests\SchoolGroupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolGroupController extends Controller
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

        if (!$school) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $query = SchoolGroup::with('schoolClass')
            ->where('school_id', $school->id);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('group_name', 'like', "%{$request->search}%")
                    ->orWhereHas('schoolClass', function ($sq) use ($request) {
                        $sq->where('class_name', 'like', "%{$request->search}%");
                    });
            });
        }

        return response()->json($query->latest()->paginate(10));
    }

    public function store(SchoolGroupRequest $request)
    {
        try {
            $school = $this->getSchool($request->user());
            if (!$school) {
                return response()->json(['message' => 'School profile not found.'], 404);
            }

            $group = DB::transaction(function () use ($school, $request) {
                $classExists = SchoolClass::where('id', $request->class_id)
                    ->where('school_id', $school->id)
                    ->exists();

                if (!$classExists) {
                    throw new \Exception('Invalid class selection.');
                }

                return SchoolGroup::create([
                    'school_id'  => $school->id,
                    'class_id'   => $request->validated()['class_id'],
                    'group_name' => $request->validated()['group_name'],
                ]);
            });

            return response()->json(['message' => 'Group created successfully', 'data' => $group]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create group.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        if (!$school) return response()->json(['message' => 'Unauthorized'], 401);

        $group = SchoolGroup::where('school_id', $school->id)->findOrFail($id);

        return response()->json($group);
    }

    public function update(SchoolGroupRequest $request, $id)
    {
        try {
            $school = $this->getSchool($request->user());
            if (!$school) return response()->json(['message' => 'Unauthorized'], 401);

            DB::transaction(function () use ($school, $id, $request) {
                $group = SchoolGroup::where('school_id', $school->id)->findOrFail($id);
                $classExists = SchoolClass::where('id', $request->validated()['class_id'])
                    ->where('school_id', $school->id)
                    ->exists();

                if (!$classExists) {
                    throw new \Exception('Invalid class selection.');
                }

                $group->update([
                    'class_id'   => $request->validated()['class_id'],
                    'group_name' => $request->validated()['group_name'],
                ]);
            });

            return response()->json(['message' => 'Group updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update group.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $school = $this->getSchool($request->user());
            if (!$school) return response()->json(['message' => 'Unauthorized'], 401);

            DB::transaction(function () use ($school, $id) {
                SchoolGroup::where('school_id', $school->id)->findOrFail($id)->delete();
            });

            return response()->json(['message' => 'Group deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete group.', 'error' => $e->getMessage()], 500);
        }
    }
    
        // School group filter
    public function groupFilter(Request $request)
    {
        $school = $this->getSchool($request->user());

        if (!$school) {
            return response()->json([
                'message' => 'School profile not found.'
            ], 404);
        }

        $query = SchoolGroup::with('schoolClass')
            ->where('school_id', $school->id);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('group_name', 'like', "%{$search}%")
                ->orWhereHas('schoolClass', function ($sq) use ($search) {
                    $sq->where('class_name', 'like', "%{$search}%");
                });
            });
        }

        $groups = $query->latest()->paginate(10);

        return response()->json($groups);
    }
    
    
    
}