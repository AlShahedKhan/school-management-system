<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdmissionStudent;
use App\Models\Principal;
use App\Models\School;
use App\Models\SchoolAdmitCardSetting;
use App\Models\SchoolExamAdmitCard;
use App\Models\SchoolExamName;
use App\Models\SchoolExamRoutine;
use App\Models\SchoolExamSeatPlan;
use App\Http\Requests\UpdateSchoolAdmitCardSettingRequest;
use App\Support\AdmitCardPdfRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SchoolExamAdmitCardController extends Controller
{
    private function getSchool()
    {
        return School::where('user_id', Auth::id())->first();
    }

    private function getSchoolId()
    {
        $school = $this->getSchool();
        return $school ? $school->id : null;
    }

    public function index(Request $request)
    {
        $school = $this->getSchool();
        $schoolId = $school?->id;

        $query = SchoolExamAdmitCard::with([
            'student:student_id_number,student_name,father_name,image'
        ])->where('school_id', $schoolId);

        // Filters
        if ($request->filled('admit_card_id')) {
            $query->whereKey($request->integer('admit_card_id'));
        }

        if ($request->filled('class_name')) {
            $query->where('class_name', $request->class_name);
        }

        if ($request->filled('group_name')) {
            $query->where('group_name', $request->group_name);
        }

        if ($request->filled('section_name')) {
            $query->where('section_name', $request->section_name);
        }

        if ($request->filled('session_name')) {
            $query->where('session_name', $request->session_name);
        }

        if ($request->filled('exam_name')) {
            $query->where('exam_name', $request->exam_name);
        }

        // Search
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';

            $query->where(function ($q) use ($searchTerm) {
                $q->where('student_id_number', 'like', $searchTerm)
                    ->orWhereHas('student', function ($student) use ($searchTerm) {
                        $student->where('student_name', 'like', $searchTerm);
                    });
            });
        }

        $perPage = $request->input('per_page', 10);

        $paginatedData = $query->latest()->paginate($perPage);

        // Add student info to response
        $paginatedData->getCollection()->transform(function ($item) {
            $item->student_name = optional($item->student)->student_name;
            $item->father_name = optional($item->student)->father_name;
            $item->student_image = optional($item->student)->image;

            unset($item->student);

            return $item;
        });

        $response = $paginatedData->toArray();

        // Seat plans use the same displayed class/group/section/session/exam values
        // as admit cards, so attach the matching seat without changing the card schema.
        if ($schoolId && !empty($response['data'])) {
            $seatKey = static function (array $values): string {
                return implode('|', array_map(
                    static fn ($value) => strtolower(trim((string) ($value ?? ''))),
                    $values
                ));
            };

            $studentIds = collect($response['data'])
                ->pluck('student_id_number')
                ->filter()
                ->values();

            $seatPlans = SchoolExamSeatPlan::query()
                ->where('school_id', $schoolId)
                ->whereIn('student_id_number', $studentIds)
                ->get([
                    'student_id_number',
                    'class_name',
                    'group_name',
                    'section_name',
                    'session_name',
                    'exam_name',
                    'seat_number',
                ])
                ->keyBy(fn ($seat) => $seatKey([
                    $seat->student_id_number,
                    $seat->class_name,
                    $seat->group_name,
                    $seat->section_name,
                    $seat->session_name,
                    $seat->exam_name,
                ]));

            foreach ($response['data'] as &$card) {
                $seat = $seatPlans->get($seatKey([
                    $card['student_id_number'],
                    $card['class_name'],
                    $card['group_name'],
                    $card['section_name'],
                    $card['session_name'],
                    $card['exam_name'],
                ]));

                $card['seat_number'] = $seat?->seat_number;
            }
            unset($card);
        }

        // Fetch routines
        $routines = collect();

        if ($schoolId && !empty($response['data'])) {

            $combinations = [];

            foreach ($response['data'] as $card) {
                $key = implode('|', [
                    $card['class_name'],
                    $card['session_name'],
                    $card['exam_name']
                ]);

                $combinations[$key] = [
                    'class_name' => $card['class_name'],
                    'session_name' => $card['session_name'],
                    'exam_name' => $card['exam_name']
                ];
            }

            // Routines now store foreign keys. Flatten their relationships because the
            // admit-card preview still groups routines by their displayed names.
            $routines = SchoolExamRoutine::with([
                'schoolClass', 'schoolGroup', 'schoolSection', 'schoolSession', 'schoolExam', 'schoolSubject',
            ])
                ->where('school_id', $schoolId)
                ->orderBy('exam_date')
                ->orderBy('start_time')
                ->get()
                ->map(function (SchoolExamRoutine $routine) {
                    $routine->class_name = $routine->schoolClass?->class_name;
                    $routine->group_name = $routine->schoolGroup?->group_name;
                    $routine->section_name = $routine->schoolSection?->section_name;
                    $routine->session_name = $routine->schoolSession?->session_year;
                    $routine->exam_name = $routine->schoolExam?->exam_name;
                    $routine->subject_name = $routine->schoolSubject?->subject_name;

                    return $routine;
                })
                ->filter(function (SchoolExamRoutine $routine) use ($combinations) {
                    return isset($combinations[implode('|', [
                        $routine->class_name,
                        $routine->session_name,
                        $routine->exam_name,
                    ])]);
                })
                ->values();
        }

        $response['routines'] = $routines;

        // School Info
        if ($school) {

            $principal = Principal::where('school_id', $schoolId)->first();

            $response['school_info'] = [
                'school_name' => $school->school_name,
                'village' => $school->village,
                'upazila' => $school->upazila,
                'district' => $school->district,
                'division' => $school->division,
                'full_address' => collect([
                    $school->village,
                    $school->upazila,
                    $school->district,
                    $school->division,
                ])->filter()->implode(', '),
                'mobile' => $school->mobile,
                'email' => $school->email,
                'logo' => $school->logo
                    ? asset('storage/' . $school->logo)
                    : null,
                'principal_signature' => $principal && $principal->signature
                    ? asset('storage/' . $principal->signature)
                    : null,
            ];
        }

        return response()->json($response);
    }

    /**
     * Return one admit card for edit/details requests made by the resource route.
     */
    public function show(int $id)
    {
        $schoolId = $this->getSchoolId();

        $admitCard = SchoolExamAdmitCard::with('student:student_id_number,student_name,father_name,image')
            ->where('school_id', $schoolId)
            ->findOrFail($id);

        $payload = $admitCard->toArray();
        $student = $admitCard->student;
        $payload['student_name'] = $student?->student_name;
        $payload['father_name'] = $student?->father_name;
        $payload['student_image'] = $student?->image;
        unset($payload['student']);

        return response()->json($payload);
    }

    public function preview(Request $request)
    {
        $payload = $this->documentPayload($request);

        if ($payload instanceof \Illuminate\Http\JsonResponse) {
            return $payload;
        }

        $token = Str::random(64);
        Cache::put('admit-card-preview:'.$token, $payload + [
            'user_id' => Auth::id(),
            'showToolbar' => false,
        ], now()->addMinutes(2));

        return response()->json([
            'url' => URL::temporarySignedRoute(
                'internal.school.admit-card-preview',
                now()->addMinutes(2),
                ['token' => $token]
            ),
        ]);
    }

    public function settings()
    {
        $school = $this->getSchool();
        abort_unless($school, 404);

        return response()->json(SchoolAdmitCardSetting::forSchool($school->id));
    }

    public function updateSettings(UpdateSchoolAdmitCardSettingRequest $request)
    {
        $school = $this->getSchool();
        abort_unless($school, 404);

        $settings = SchoolAdmitCardSetting::forSchool($school->id);
        $settings->update($request->validated());

        return response()->json(['message' => 'Admit card instructions updated successfully.', 'data' => $settings->fresh()]);
    }

    public function exportPdf(Request $request, AdmitCardPdfRenderer $pdfRenderer)
    {
        $payload = $this->documentPayload($request);

        if ($payload instanceof \Illuminate\Http\JsonResponse) {
            return $payload;
        }

        $token = Str::random(64);
        $cacheKey = 'admit-card-preview:'.$token;
        Cache::put($cacheKey, $payload + ['user_id' => Auth::id(), 'showToolbar' => false], now()->addMinutes(2));
        $previewUrl = URL::temporarySignedRoute(
            'internal.school.admit-card-preview',
            now()->addMinutes(2),
            ['token' => $token]
        );

        try {
            $pdf = $pdfRenderer->render($previewUrl);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json(['message' => 'The admit-card PDF could not be generated. Please try again.'], 500);
        } finally {
            Cache::forget($cacheKey);
        }

        $cards = $payload['cards'];
        $filename = count($cards) === 1
            ? 'admit-card-'.($cards[0]['admit_card_number'] ?? now()->format('Ymd-His')).'.pdf'
            : 'admit-cards-'.now()->format('Ymd-His').'.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Length' => (string) strlen($pdf),
        ]);
    }

    private function documentPayload(Request $request): array|\Illuminate\Http\JsonResponse
    {
        $language = $request->input('language', 'en');
        if (! in_array($language, ['en', 'bn'], true)) {
            throw ValidationException::withMessages(['language' => 'Language must be English or Bangla.']);
        }

        $response = $this->index($request->merge(['per_page' => 500]));
        $data = $response->getData(true);

        if (empty($data['data'])) {
            return response()->json(['message' => 'No admit cards found for the selected filters.'], 404);
        }

        $schoolModel = $this->getSchool();
        $school = $data['school_info'] ?? [];
        $school['logo_data_uri'] = $this->publicImageDataUri($schoolModel?->logo);
        $school['principal_signature_data_uri'] = $this->publicImageDataUri(
            Principal::where('school_id', $schoolModel?->id)->value('signature')
        );

        $cards = collect($data['data'])->map(function (array $card): array {
            $card['student_image_data_uri'] = $this->publicImageDataUri($card['student_image'] ?? null);
            return $card;
        })->all();
        $routines = collect($data['routines'] ?? [])->sortBy([
            ['exam_date', 'asc'], ['start_time', 'asc'],
        ])->values()->all();

        foreach ($cards as $card) {
            $count = $this->matchingRoutines($card, $routines)->count();
            if ($count > 18) {
                throw ValidationException::withMessages([
                    'routines' => "The admit card for {$card['student_name']} has {$count} routines. This design supports a maximum of 18.",
                ]);
            }
        }

        $settings = SchoolAdmitCardSetting::forSchool($schoolModel->id);

        return [
            'cards' => $cards,
            'school' => $school,
            'routines' => $routines,
            'language' => $language,
            'instructions' => $language === 'bn' ? $settings->instructions_bn : $settings->instructions_en,
        ];
    }

    private function matchingRoutines(array $card, array $routines)
    {
        $normal = static fn ($value) => strtolower(trim((string) ($value ?? '')));

        return collect($routines)->filter(function ($routine) use ($card, $normal) {
            if ($normal(data_get($routine, 'class_name')) !== $normal($card['class_name'] ?? null)
                || $normal(data_get($routine, 'session_name')) !== $normal($card['session_name'] ?? null)
                || $normal(data_get($routine, 'exam_name')) !== $normal($card['exam_name'] ?? null)) {
                return false;
            }

            $group = $normal(data_get($routine, 'group_name'));
            $section = $normal(data_get($routine, 'section_name'));

            return ($group === '' || $group === $normal($card['group_name'] ?? null))
                && ($section === '' || $section === $normal($card['section_name'] ?? null));
        });
    }

    private function publicImageDataUri(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $storagePath = preg_replace('#^.*?/storage/#', '', str_replace('\\', '/', $path));

        if (! $storagePath || ! Storage::disk('public')->exists($storagePath)) {
            return null;
        }

        $mimeType = Storage::disk('public')->mimeType($storagePath) ?: 'image/png';

        return sprintf(
            'data:%s;base64,%s',
            $mimeType,
            base64_encode(Storage::disk('public')->get($storagePath))
        );
    }

    private function validateAdmitPrerequisites($school_id, $className, $groupName, $sectionName, $sessionName, $examName)
    {
        $class = \App\Models\SchoolClass::where('school_id', $school_id)->where('class_name', $className)->first();
        $session = \App\Models\SchoolSession::where('school_id', $school_id)->where('session_year', $sessionName)->first();
        $group = $groupName ? \App\Models\SchoolGroup::where('school_id', $school_id)->where('group_name', $groupName)->first() : null;
        $section = $sectionName ? \App\Models\SchoolSection::where('school_id', $school_id)->where('section_name', $sectionName)->first() : null;

        if (!$class || !$session || ($groupName && !$group) || ($sectionName && !$section)) {
            return [
                'valid' => false,
                'message' => 'Please select valid class, group, section, and session details.'
            ];
        }

        $examQuery = SchoolExamName::where('school_id', $school_id)
            ->where('class_id', $class?->id)
            ->where('session_id', $session?->id)
            ->where('exam_name', $examName);

        if ($group) {
            $examQuery->where('group_id', $group->id);
        }

        if ($section) {
            $examQuery->where('section_id', $section->id);
        }

        $examRecord = $examQuery->first();
        if (!$examRecord) {
            return [
                'valid' => false,
                'message' => 'Please create the Exam and Exam Routine before generating the Admit Card.'
            ];
        }

        $routineFound = SchoolExamRoutine::where('school_id', $school_id)
            ->where('class_id', $class->id)
            ->where('session_id', $session->id)
            ->where('exam_id', $examRecord->id)
            ->when($group, fn ($q) => $q->where('group_id', $group->id))
            ->when($section, fn ($q) => $q->where('section_id', $section->id))
            ->exists();

        if (!$routineFound) {
            $routineFound = SchoolExamRoutine::where('school_id', $school_id)
                ->where('class_id', $class->id)
                ->where('session_id', $session->id)
                ->where('exam_id', $examRecord->id)
                ->exists();
        }

        if (!$routineFound) {
            return [
                'valid' => false,
                'message' => 'Please create the Exam and Exam Routine before generating the Admit Card.'
            ];
        }

        return ['valid' => true];
    }

    public function checkPrerequisites(Request $request)
    {
        $school_id = $this->getSchoolId();

        $validation = $this->validateAdmitPrerequisites(
            $school_id,
            $request->class_name,
            $request->group_name,
            $request->section_name,
            $request->session_name,
            $request->exam_name
        );

        if (!$validation['valid']) {
            return response()->json([
                'valid' => false,
                'message' => $validation['message']
            ]);
        }

        return response()->json(['valid' => true]);
    }

    public function store(Request $request)
    {
        $school_id = $this->getSchoolId();
        $students = $request->students;
        $exam = $request->exam_name;

        $prerequisiteCheck = $this->validateAdmitPrerequisites(
            $school_id,
            $request->class_name,
            $request->group_name,
            $request->section_name,
            $request->session_name,
            $exam
        );

        if (!$prerequisiteCheck['valid']) {
            return response()->json([
                'status' => 'missing_modules',
                'message' => $prerequisiteCheck['message']
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Lock to prevent race condition when multiple schools submit simultaneously
            DB::statement('SELECT GET_LOCK("admit_card_seq", 15)');

            $maxNumber = SchoolExamAdmitCard::max('admit_card_number');
            $currentAdmitNumber = $maxNumber ? (int)$maxNumber + 1 : 24951080;

            foreach ($students as $student) {
                $studentId = $student['student_id_number'];

                $exists = SchoolExamAdmitCard::where([
                    'school_id' => $school_id,
                    'class_name' => $request->class_name,
                    'exam_name' => $exam,
                    'student_id_number' => $studentId,
                ])->exists();

                if ($exists) {
                    DB::statement('SELECT RELEASE_LOCK("admit_card_seq")');
                    DB::rollBack();
                    return response()->json([
                        'status' => 'exists',
                        'message' => "Admit card already exists for Student ID: {$studentId} for this exam."
                    ], 422);
                }

                SchoolExamAdmitCard::create([
                    'school_id' => $school_id,
                    'class_name' => $request->class_name,
                    'group_name' => $request->group_name,
                    'section_name' => $request->section_name,
                    'session_name' => $request->session_name,
                    'exam_name' => $exam,
                    'student_id_number' => $studentId,
                    'admit_card_number' => $currentAdmitNumber,
                ]);

                $currentAdmitNumber++;
            }

            DB::statement('SELECT RELEASE_LOCK("admit_card_seq")');
            DB::commit();
            return response()->json(['message' => 'Admit cards generated successfully.']);
        } catch (\Throwable $e) {
            DB::statement('SELECT RELEASE_LOCK("admit_card_seq")');
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $school_id = $this->getSchoolId();
        $admit = SchoolExamAdmitCard::where('school_id', $school_id)->findOrFail($id);

        $admit->update([
            'exam_name' => $request->exam_name,
            'class_name' => $request->class_name,
            'section_name' => $request->section_name,
            'group_name' => $request->group_name,
            'session_name' => $request->session_name,
            // 'admit_card_number' remains fixed to prevent ID jumping on simple edits
        ]);

        return response()->json(['message' => 'Admit card updated successfully']);
    }

    public function destroy($id)
    {
        $school_id = $this->getSchoolId();
        SchoolExamAdmitCard::where('school_id', $school_id)->where('id', $id)->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function getStudents(Request $request)
    {
        $school_id = $this->getSchoolId();
        $students = AdmissionStudent::where('school_id', $school_id)
            // Exclude Inactive students from admit card generation
            ->where('status', '!=', 'Inactive')
            ->where('class', $request->class_name)
            ->where('session', $request->session_name)
            ->get(['student_id_number', 'student_name']);

        return response()->json(['data' => $students]);
    }
}
