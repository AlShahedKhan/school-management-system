<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdmissionStudent;
use App\Models\User;
use App\Models\Guardian;
use App\Models\Teacher;
use App\Models\TeacherClassPermission;
use App\Models\StudentPromotion;
use App\Models\StudentReadmission;
use App\Enums\StudentStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class SchoolStudentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $schoolId = match ($user->role) {
            'teacher' => (Teacher::where('id_number', $user->id_number)->first()?->school_id),
            default => $user->id,
        };
        $schoolName = $user->school_name;
        $query = AdmissionStudent::with([
            'schoolClass',
            'schoolSection',
            'schoolGroup',
            'schoolSession'
        ])->where(function ($q) use ($schoolName, $schoolId) {
            $q->where('school_id', $schoolId)
                ->orWhere('school', $schoolName);
        })->when($user->role === 'teacher', function ($q) use ($user) {
            $teacher = Teacher::where('id_number', $user->id_number)->first();
            if (!$teacher) {
                $q->whereRaw('1 = 0');
                return;
            }
            $permissions = TeacherClassPermission::where('teacher_id', $teacher->id)->get();
            if ($permissions->isEmpty()) {
                $q->whereRaw('1 = 0');
                return;
            }
            $q->where(function ($sq) use ($permissions) {
                foreach ($permissions as $permission) {
                    $sq->orWhere(function ($ssq) use ($permission) {
                        $ssq->where('class', $permission->class_id);
                        if ($permission->group_id) {
                            $ssq->where('group', $permission->group_id);
                        }
                        if ($permission->section_id) {
                            $ssq->where('section', $permission->section_id);
                        }
                    });
                }
            });
        })->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($sq) use ($search) {
                $sq->where('student_name', 'like', "%$search%")
                    ->orWhere('student_id_number', 'like', "%$search%")
                    ->orWhere('admission_id', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        })->when($request->filled('class'), fn($q) => $q->where('class', $request->class))
          ->when($request->filled('group'), fn($q) => $q->where('group', $request->group))
          ->when($request->filled('section'), fn($q) => $q->where('section', $request->section))
          ->when($request->filled('session'), fn($q) => $q->where('session', $request->session));
        if ($request->boolean('all')) {
            $query->where('status', '!=', StudentStatus::Inactive->value);
            $students = $query->orderBy('id', 'desc')->get();
            $studentIds = $students->pluck('id')->toArray();
            $promotedIds = StudentPromotion::whereIn('student_id', $studentIds)->pluck('student_id')->toArray();
            $readmittedIds = StudentReadmission::whereIn('student_id', $studentIds)->pluck('student_id')->toArray();
            $students->map(function ($student) use ($promotedIds, $readmittedIds) {
                $student->class_name = $student->schoolClass->class_name ?? $student->class;
                $student->section_name = $student->schoolSection->section_name ?? $student->section;
                $student->group_name = $student->schoolGroup->group_name ?? $student->group;
                $student->session_year = $student->schoolSession->session_year ?? $student->session;
                $student->student_type = match (true) {
                    in_array($student->id, $promotedIds) => 'Promote',
                    in_array($student->id, $readmittedIds) => 'Re-Admission',
                    $student->admission_fee === 'N/A' => 'Bulk Upload',
                    default => 'Admission',
                };
                return $student;
            });
            return response()->json($students);
        }
        $students = $query->orderBy('id', 'desc')->paginate(10);
        $studentIds = $students->getCollection()->pluck('id')->toArray();
        $promotedIds = StudentPromotion::whereIn('student_id', $studentIds)->pluck('student_id')->toArray();
        $readmittedIds = StudentReadmission::whereIn('student_id', $studentIds)->pluck('student_id')->toArray();
        $students->getCollection()->transform(function ($student) use ($promotedIds, $readmittedIds) {
            $student->class_name = $student->schoolClass->class_name ?? $student->class;
            $student->section_name = $student->schoolSection->section_name ?? $student->section;
            $student->group_name = $student->schoolGroup->group_name ?? $student->group;
            $student->session_year = $student->schoolSession->session_year ?? $student->session;
            $student->student_type = match (true) {
                in_array($student->id, $promotedIds) => 'Promote',
                in_array($student->id, $readmittedIds) => 'Re-Admission',
                $student->admission_fee === 'N/A' => 'Bulk Upload',
                default => 'Admission',
            };
            return $student;
        });
        return response()->json($students);
    }
    public function updateStatus(Request $request, $id)
    {
        $schoolName = Auth::user()->school_name;
        $schoolId = Auth::id();
        $student = AdmissionStudent::where(function ($q) use ($schoolName, $schoolId) {
            $q->where('school', $schoolName)->orWhere('school_id', $schoolId);
        })->findOrFail($id);
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,Active,Inactive,approved,rejected',
            'inactive_date' => 'nullable|date',
            'inactive_reason' => 'nullable|string',
            'active_date' => 'nullable|date'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $statusEnum = StudentStatus::tryFrom($request->status);
        $newStatus = match ($statusEnum) {
            StudentStatus::Approved => StudentStatus::Active,
            default => $statusEnum,
        };
        match ($newStatus) {
            StudentStatus::Inactive => $student->fill([
                'status' => StudentStatus::Inactive->value,
                'inactive_date' => $request->input('inactive_date') ?? now()->toDateString(),
                'inactive_reason' => $request->input('inactive_reason'),
                'status_updated_by' => Auth::id(),
            ]),
            StudentStatus::Active => $student->fill([
                'status' => StudentStatus::Active->value,
                'active_date' => $request->input('active_date') ?? now()->toDateString(),
                'status_updated_by' => Auth::id(),
            ]),
            default => $student->fill([
                'status' => $newStatus->value,
            ]),
        };
        $student->save();
        return response()->json([
            'message' => 'Student status updated to ' . $student->status,
            'status' => $student->status,
            'inactive_since' => $student->inactive_date
        ]);
    }
    public function show($id)
    {
        $schoolId = Auth::id();
        $student = AdmissionStudent::with([
            'schoolClass',
            'schoolSection',
            'schoolGroup',
            'schoolSession'
        ])->where('school_id', $schoolId)->findOrFail($id);
        $student->class_name = $student->schoolClass->class_name ?? $student->class;
        $student->section_name = $student->schoolSection->section_name ?? $student->section;
        $student->group_name = $student->schoolGroup->group_name ?? $student->group;
        $student->session_year = $student->schoolSession->session_year ?? $student->session;
        return response()->json($student);
    }
    public function showDetails($id)
    {
        $schoolId = Auth::id();
        $student = AdmissionStudent::with([
            'schoolClass',
            'schoolSection',
            'schoolGroup',
            'schoolSession'
        ])->where('school_id', $schoolId)->findOrFail($id);
        $student->class_name = $student->schoolClass->class_name ?? $student->class;
        $student->section_name = $student->schoolSection->section_name ?? $student->section;
        $student->group_name = $student->schoolGroup->group_name ?? $student->group;
        $student->session_year = $student->schoolSession->session_year ?? $student->session;
        $isPromoted = StudentPromotion::where('student_id', $student->id)->exists();
        $isReadmitted = StudentReadmission::where('student_id', $student->id)->exists();
        $student->student_type = match (true) {
            $isPromoted => 'Promote',
            $isReadmitted => 'Re-Admission',
            $student->admission_fee === 'N/A' => 'Bulk Upload',
            default => 'Admission',
        };
        $guardian = Guardian::where('id', $student->guardian_id)->first();
        return response()->json([
            'student' => $student,
            'guardian' => $guardian
        ]);
    }
    public function updateDetails(Request $request, $id)
    {
        $student = AdmissionStudent::where('school_id', Auth::id())->findOrFail($id);
        $data = $request->except(['id', 'school_id', 'image', 'password']);
        $student->update($data);
        return response()->json(['message' => 'Profile Updated Successfully']);
    }
    public function update(Request $request, $id)
    {
        $schoolName = Auth::user()->school_name;
        $schoolId = Auth::id();
        $student = AdmissionStudent::where(function ($q) use ($schoolName, $schoolId) {
            $q->where('school', $schoolName)->orWhere('school_id', $schoolId);
        })->findOrFail($id);
        $validator = Validator::make($request->all(), [
            'student_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'class' => 'required|exists:school_classes,id',
            'section' => 'required|exists:school_sections,id',
            'session' => 'required|exists:school_sessions,id',
            'group' => 'nullable|exists:school_groups,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'admission_fee' => 'required',
            'admission_date' => 'required|date',
            'current_country' => 'required|string|max:100',
            'current_division' => 'required|string|max:100',
            'current_district' => 'required|string|max:100',
            'current_upazila' => 'required|string|max:100',
            'current_village' => 'required|string|max:255',
            'permanent_country' => 'required|string|max:100',
            'permanent_division' => 'required|string|max:100',
            'permanent_district' => 'required|string|max:100',
            'permanent_upazila' => 'required|string|max:100',
            'permanent_village' => 'required|string|max:255',
            'g_name' => 'required|string|max:255',
            'g_relation' => 'required|string|max:255',
            'g_mobile' => 'required|string|max:20',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $student->student_name = $request->student_name;
        $student->father_name = $request->father_name;
        $student->mother_name = $request->mother_name;
        $student->mobile = $request->mobile;
        $student->class = $request->class;
        $student->session = $request->session;
        $student->section = $request->section;
        $student->group = $request->group;
        if ($request->filled('admission_fee')) $student->admission_fee = $request->admission_fee;
        if ($request->filled('admission_date')) $student->admission_date = $request->admission_date;
        if ($request->has('current_country')) $student->current_country = $request->current_country;
        if ($request->has('current_division')) $student->current_division = $request->current_division;
        if ($request->has('current_district')) $student->current_district = $request->current_district;
        if ($request->has('current_upazila')) $student->current_upazila = $request->current_upazila;
        if ($request->has('current_village')) $student->current_village = $request->current_village;
        if ($request->has('permanent_country')) $student->permanent_country = $request->permanent_country;
        if ($request->has('permanent_division')) $student->permanent_division = $request->permanent_division;
        if ($request->has('permanent_district')) $student->permanent_district = $request->permanent_district;
        if ($request->has('permanent_upazila')) $student->permanent_upazila = $request->permanent_upazila;
        if ($request->has('permanent_village')) $student->permanent_village = $request->permanent_village;
        
        // Update or create Guardian
        if ($request->filled(['g_name', 'g_relation', 'g_mobile'])) {
            if ($student->guardian_id) {
                $guardian = Guardian::find($student->guardian_id);
                if ($guardian) {
                    $guardian->update([
                        'name' => $request->g_name,
                        'relation' => $request->g_relation,
                        'mobile' => $request->g_mobile,
                    ]);
                }
            } else {
                $guardian = Guardian::create([
                    'name' => $request->g_name,
                    'relation' => $request->g_relation,
                    'mobile' => $request->g_mobile,
                ]);
                $student->guardian_id = $guardian->id;
            }
        }

        if ($request->hasFile('image')) {
            if ($student->image && file_exists(public_path('storage/' . $student->image))) {
                unlink(public_path('storage/' . $student->image));
            }
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/students'), $filename);
            $student->image = 'students/' . $filename;
        }
        $student->save();
        return response()->json(['message' => 'Student updated successfully']);
    }
    public function destroy($id)
    {
        $schoolName = Auth::user()->school_name;
        $student = AdmissionStudent::where('school', $schoolName)->findOrFail($id);
        try {
            DB::transaction(function () use ($student, $schoolName) {
                User::where('school_name', $schoolName)
                    ->where('id_number', $student->student_id_number)
                    ->where('role', 'student')
                    ->delete();
                if ($student->image && file_exists(public_path('storage/' . $student->image))) {
                    unlink(public_path('storage/' . $student->image));
                }
                $guardianId = $student->guardian_id;
                $student->delete();
                if ($guardianId) {
                    $hasOtherStudents = AdmissionStudent::where('guardian_id', $guardianId)->exists();
                    if (!$hasOtherStudents) {
                        Guardian::where('id', $guardianId)->delete();
                    }
                }
            });
            return response()->json(['message' => 'Student and associated accounts removed successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete student data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}