<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeacherClassPermission;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolTeacherClassPermissionController extends Controller
{
    private function getSchool($user)
    {

        if ($user->role === 'teacher') {

            $teacher = Teacher::where('id_number', $user->id_number)->first();

            if ($teacher) {

                return \App\Models\School::find($teacher->school_id);

            }

        }

        return \App\Models\School::where('user_id', $user->id)->first();

    }

    public function index(Request $request)
    {

        $school = $this->getSchool($request->user());

        $query = TeacherClassPermission::with(['school_class', 'school_group', 'school_section', 'school_subject'])
            ->where('school_id', $school->id);

        $user = $request->user();

        if ($user->role === 'teacher') {

            $teacher = Teacher::where('id_number', $user->id_number)->first();

            if ($teacher) {

                $query->where('teacher_id', $teacher->id);

            }

        }

        if ($request->teacher_id) {

            $query->where('teacher_id', $request->teacher_id);

        }

        if ($request->search) {

            $s = $request->search;

            $query->where(function ($q) use ($s) {

                $q->where('teacher_name', 'like', "%$s%")
                    ->orWhere('teacher_id_number', 'like', "%$s%");

            });

        }

        return response()->json($query->latest()->paginate(15));

    }

    public function store(Request $request)
    {
        $school = $this->getSchool($request->user());
        $teacher = Teacher::findOrFail($request->teacher_id);

        $data = $request->validate([
            'teacher_id' => 'required',
            'class_id' => 'required',
            'subject_id' => 'required',
            'group_id' => 'nullable',
            'section_id' => 'nullable',
        ]);

        $data['school_id'] = $school->id;
        $data['teacher_name'] = $teacher->name;
        $data['teacher_designation'] = $teacher->designation;
        $data['teacher_id_number'] = $teacher->id_number;
        $data['teacher_photo'] = $teacher->photo;

        $permission = TeacherClassPermission::create($data);
        return response()->json($permission);
    }

    public function show($id)
    {
        return TeacherClassPermission::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $permission = TeacherClassPermission::findOrFail($id);
        $teacher = Teacher::findOrFail($request->teacher_id);

        $data = $request->all();
        $data['teacher_name'] = $teacher->name;
        $data['teacher_designation'] = $teacher->designation;
        $data['teacher_id_number'] = $teacher->id_number;
        $data['teacher_photo'] = $teacher->photo;

        $permission->update($data);
        return response()->json($permission);
    }

    public function destroy($id)
    {
        TeacherClassPermission::destroy($id);
        return response()->json(['message' => 'Permission Revoked']);
    }
}
