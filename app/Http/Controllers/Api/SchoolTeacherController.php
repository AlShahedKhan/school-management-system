<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\School;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SchoolTeacherController extends Controller
{
    protected $smsService;

    /**
     * Inject the SmsService
     */
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    // List teachers with Pagination
    public function index(Request $request)
    {
        $school = School::where('user_id', $request->user()->id)->first();
        if (!$school) {
            return response()->json(['message' => 'Invalid user school profile'], 400);
        }
        $schoolId = $school->id;

        $query = Teacher::where('school_id', $schoolId);

        // Added on 2026-07-11: Filter by status if provided (e.g. for active status dropdowns)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('designation', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        }

        if ($request->filled('designation')) {
            $query->where('designation', $request->designation);
        }

        if ($request->filled('teacher_id')) {
            $query->whereKey($request->teacher_id);
        }

        // Added on 2026-07-11: Support all=true to fetch all matching records without pagination
        if ($request->boolean('all')) {
            $teachers = $query->orderBy('name', 'asc')->get();
            return response()->json(['data' => $teachers]);
        }

        $teachers = $query->orderBy('created_at', 'asc')->paginate(10);
        return response()->json($teachers);
    }



    public function store(Request $request)
    {
        $currentUser = Auth::user();
        $school = School::where('user_id', $currentUser->id)->first();

        if (!$school) {
            return response()->json(['message' => 'Invalid school profile'], 400);
        }

        // Confirmed on 2026-07-11: Validate required fields (name, mobile, email) and photo image upload size limit (max 2MB)
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'mobile' => ['required', 'string', 'regex:/^[0-9]+$/', 'unique:teachers,mobile', 'unique:users,mobile'],
            'email' => 'required|email|unique:teachers,email',
            'photo' => 'nullable|image|max:2048'
        ], [
            'mobile.regex' => 'Must provide numbers only.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = DB::transaction(function () use ($request, $school) {

                $photoPath = null;
                if ($request->hasFile('photo')) {
                    $photoPath = $request->file('photo')->store('teacher_photos', 'public');
                }

                // Create Teacher record
                // Modified on 2026-07-11: ID number generated in model observer via common serial
                $teacher = Teacher::create([
                    'school_id' => $school->id,
                    'name' => $request->name,
                    'designation' => $request->designation,
                    'mobile' => $request->mobile,
                    'email' => $request->email,
                    'salary_amount' => $request->salary_amount,
                    'salary_start_date' => $request->salary_start_date,
                    'pay_date' => $request->pay_date,
                    'password' => '00000000',
                    'photo' => $photoPath
                ]);

                // Create User record for login
                User::create([
                    'role' => 'teacher',
                    'name' => $request->name,
                    'school_name' => $school->school_name,
                    'mobile' => $request->mobile,
                    'id_number' => $teacher->id_number,
                    'password' => Hash::make('00000000'),
                ]);

                // Write history logs in teacher_status_logs
                // Added on 2026-07-11: Log teacher creation status history
                \App\Models\TeacherStatusLog::create([
                    'school_id' => $school->id,
                    'teacher_id' => $teacher->id,
                    'status' => 'Active',
                    'action' => 'Created',
                    'changed_by' => Auth::id(),
                    'notes' => 'Teacher account registered in school portal.'
                ]);

                return [
                    'mobile' => $request->mobile,
                    'school_name' => $school->school_name,
                    'teacher_id' => $teacher->id_number,
                    'teacher' => $teacher
                ];
            });

            // Send SMS notification
            $smsResponse = $this->sendTeacherSMS($school, $result['mobile'], $result['school_name'], $result['teacher_id']);

            return response()->json([
                'message' => 'Teacher created successfully. ID: ' . $result['teacher_id'],
                'teacher' => $result['teacher'],
                'sms_status' => [
                    'success' => $smsResponse['success'],
                    'message' => $smsResponse['success'] ? 'SMS Sent' : ($smsResponse['message'] ?? 'SMS Failed'),
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Create Teacher Error: ' . $e->getMessage());

            // Return 422 so the frontend SweetAlert captures the specific SQL/PHP error as a validation detail
            return response()->json([
                'errors' => [
                    'exception' => [$e->getMessage()]
                ],
                'message' => 'Failed to create teacher'
            ], 422);
        }
    }

    public function show(Request $request, $id)
    {
        $school = School::where('user_id', $request->user()->id)->first();
        $teacher = Teacher::where('school_id', $school->id)->findOrFail($id);
        return response()->json($teacher);
    }

    public function update(Request $request, $id)
    {
        $school = School::where('user_id', $request->user()->id)->first();

        try {
            $teacher = Teacher::where('school_id', $school->id)->findOrFail($id);

            // Confirmed on 2026-07-11: Validate required fields (name, mobile, email) and photo image upload size limit (max 2MB) on update
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'designation' => 'nullable|string|max:255',
                'mobile' => ['required', 'string', 'regex:/^[0-9]+$/', 'unique:teachers,mobile,' . $teacher->id],
                'email' => 'required|email|unique:teachers,email,' . $teacher->id,
                'password' => 'nullable|string|min:6|confirmed',
                'photo' => 'nullable|image|max:2048'
            ], [
                'mobile.regex' => 'Must provide numbers only.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            DB::transaction(function () use ($request, $teacher) {
                if ($request->hasFile('photo')) {
                    if ($teacher->photo && Storage::disk('public')->exists($teacher->photo)) {
                        Storage::disk('public')->delete($teacher->photo);
                    }
                    $teacher->photo = $request->file('photo')->store('teacher_photos', 'public');
                }

                $teacher->name = $request->name;
                $teacher->designation = $request->designation;
                $teacher->mobile = $request->mobile;
                $teacher->email = $request->email;
                $teacher->salary_amount = $request->salary_amount;
                $teacher->salary_start_date = $request->salary_start_date;
                $teacher->pay_date = $request->pay_date;
                $teacher->save();

                $updateData = [
                    'mobile' => $request->mobile,
                    'name' => $request->name
                ];

                if ($request->password) {
                    $hashedPassword = Hash::make($request->password);
                    $teacher->password = $hashedPassword;
                    $updateData['password'] = $hashedPassword;
                }

                // Sync with User Table via id_number
                User::where('id_number', $teacher->id_number)
                    ->where('role', 'teacher')
                    ->update($updateData);

                $teacher->save();
            });

            return response()->json(['message' => 'Teacher updated successfully', 'teacher' => $teacher]);
        } catch (\Exception $e) {
            Log::error('Update Teacher Error: ' . $e->getMessage());
            return response()->json([
                'errors' => [
                    'exception' => [$e->getMessage()]
                ],
                'message' => 'Failed to update teacher'
            ], 422);
        }
    }

    public function destroy(Request $request, $id)
    {
        $school = School::where('user_id', $request->user()->id)->first();

        try {
            DB::transaction(function () use ($school, $id) {
                $teacher = Teacher::where('school_id', $school->id)->findOrFail($id);

                if ($teacher->photo && Storage::disk('public')->exists($teacher->photo)) {
                    Storage::disk('public')->delete($teacher->photo);
                }

                // Delete login user via id_number
                User::where('id_number', $teacher->id_number)
                    ->where('role', 'teacher')
                    ->delete();

                $teacher->delete();
            });

            return response()->json(['message' => 'Teacher deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Delete Teacher Error: ' . $e->getMessage());
            return response()->json([
                'errors' => [
                    'exception' => [$e->getMessage()]
                ],
                'message' => 'Failed to delete teacher'
            ], 422);
        }
    }

    public function deactivate(Request $request, $id)
    {
        $currentUser = Auth::user();
        $school = School::where('user_id', $currentUser->id)->first();
        if (!$school) {
            return response()->json(['message' => 'Invalid school profile'], 400);
        }

        // Validate the replacement teacher's data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'mobile' => 'required|string|unique:teachers,mobile|unique:users,mobile',
            'email' => 'required|email|unique:teachers,email',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $oldTeacher = Teacher::where('school_id', $school->id)->findOrFail($id);
            if ($oldTeacher->status === \App\Enums\TeacherStatus::Hold) {
                return response()->json(['message' => 'Teacher is already deactivated.'], 400);
            }

            $result = DB::transaction(function () use ($request, $school, $oldTeacher, $currentUser) {
                // 1. Create photo path for new teacher
                $photoPath = null;
                if ($request->hasFile('photo')) {
                    $photoPath = $request->file('photo')->store('teacher_photos', 'public');
                }

                // 2. Create the replacement Teacher (Active status)
                // ID number will be generated in boot observer via common serial
                $newTeacher = Teacher::create([
                    'school_id' => $school->id,
                    'name' => $request->name,
                    'designation' => $request->designation,
                    'mobile' => $request->mobile,
                    'email' => $request->email,
                    'password' => '00000000',
                    'dob' => null,
                    'photo' => $photoPath,
                    'status' => \App\Enums\TeacherStatus::Active,
                ]);

                // 3. Create the corresponding User for replacement teacher
                User::create([
                    'role' => 'teacher',
                    'name' => $request->name,
                    'school_name' => $school->school_name,
                    'mobile' => $request->mobile,
                    'id_number' => $newTeacher->id_number,
                    'password' => Hash::make('00000000'),
                ]);

                // 4. Update old teacher status to Hold
                $oldTeacher->update([
                    'status' => \App\Enums\TeacherStatus::Hold
                ]);

                // 5. Transfer permissions (teacher_class_permissions)
                \App\Models\TeacherClassPermission::where('teacher_id', $oldTeacher->id)
                    ->update([
                        'teacher_id' => $newTeacher->id,
                        'teacher_name' => $newTeacher->name,
                        'teacher_designation' => $newTeacher->designation ?? '',
                        'teacher_id_number' => $newTeacher->id_number,
                        'teacher_photo' => $newTeacher->photo,
                    ]);

                // 6. Transfer school routines (school_routines)
                DB::table('school_routines')
                    ->where('teacher_id', $oldTeacher->id)
                    ->update([
                        'teacher_id' => $newTeacher->id
                    ]);

                // 7. Write history logs in teacher_status_logs
                // Log deactivation of old teacher
                \App\Models\TeacherStatusLog::create([
                    'school_id' => $school->id,
                    'teacher_id' => $oldTeacher->id,
                    'status' => 'Hold',
                    'action' => 'Replaced',
                    'changed_by' => $currentUser->id,
                    'replacement_teacher_id' => $newTeacher->id,
                    'notes' => "Replaced by {$newTeacher->name} (ID: {$newTeacher->id_number})"
                ]);

                // Log creation of new teacher
                \App\Models\TeacherStatusLog::create([
                    'school_id' => $school->id,
                    'teacher_id' => $newTeacher->id,
                    'status' => 'Active',
                    'action' => 'Created',
                    'changed_by' => $currentUser->id,
                    'notes' => "Created as replacement for {$oldTeacher->name} (ID: {$oldTeacher->id_number})"
                ]);

                return [
                    'mobile' => $newTeacher->mobile,
                    'school_name' => $school->school_name,
                    'teacher_id' => $newTeacher->id_number,
                    'new_teacher' => $newTeacher
                ];
            });

            // Send registration SMS to the new teacher
            $smsResponse = $this->sendTeacherSMS($school, $result['mobile'], $result['school_name'], $result['teacher_id']);

            return response()->json([
                'message' => 'Teacher deactivated and replaced successfully.',
                'new_teacher' => $result['new_teacher'],
                'sms_status' => [
                    'success' => $smsResponse['success'],
                    'message' => $smsResponse['success'] ? 'SMS Sent' : ($smsResponse['message'] ?? 'SMS Failed'),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Deactivate Teacher Error: ' . $e->getMessage());
            return response()->json([
                'errors' => ['exception' => [$e->getMessage()]],
                'message' => 'Failed to deactivate and replace teacher'
            ], 422);
        }
    }

    public function activate(Request $request, $id)
    {
        $currentUser = Auth::user();
        $school = School::where('user_id', $currentUser->id)->first();
        if (!$school) {
            return response()->json(['message' => 'Invalid school profile'], 400);
        }

        try {
            $teacher = Teacher::where('school_id', $school->id)->findOrFail($id);
            if ($teacher->status === \App\Enums\TeacherStatus::Active) {
                return response()->json(['message' => 'Teacher is already active.'], 400);
            }

            DB::transaction(function () use ($school, $teacher, $currentUser) {
                $teacher->update([
                    'status' => \App\Enums\TeacherStatus::Active
                ]);

                \App\Models\TeacherStatusLog::create([
                    'school_id' => $school->id,
                    'teacher_id' => $teacher->id,
                    'status' => 'Active',
                    'action' => 'Activated',
                    'changed_by' => $currentUser->id,
                    'notes' => 'Teacher account reactivated manually.'
                ]);
            });

            return response()->json([
                'message' => 'Teacher reactivated successfully.',
                'teacher' => $teacher
            ]);

        } catch (\Exception $e) {
            Log::error('Activate Teacher Error: ' . $e->getMessage());
            return response()->json([
                'errors' => ['exception' => [$e->getMessage()]],
                'message' => 'Failed to reactivate teacher'
            ], 422);
        }
    }

    private function sendTeacherSMS($school, $mobile, $schoolName, $teacherId)
    {
        $defaultMessage = "Welcome! Your registration at {school_name} is confirmed. Teacher ID: {teacher_id}. You can now login to your portal. Regards, {school_name}.";
        return $this->smsService->triggerEventSms($school, $mobile, 'teacher_registration', [
            '{school_name}' => $schoolName,
            '{teacher_id}' => $teacherId
        ], $defaultMessage);
    }
}
