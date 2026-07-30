<?php

namespace App\Http\Controllers\Api;

use App\Exports\ExamNameExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolExamNameRequest;
use App\Models\SchoolExamName;
use App\Models\School;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class SchoolExamNameController extends Controller
{
    private function getSchoolId(): int
    {
        $schoolId = School::where('user_id', Auth::id())->value('id');

        if (! $schoolId) {
            abort(403, 'School profile not found.');
        }

        return (int) $schoolId;
    }

    public function index(Request $request)
    {
        $query = SchoolExamName::where('school_id', $this->getSchoolId())
            ->with(['schoolClass', 'schoolGroup', 'schoolSection', 'schoolSession']);

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('exam_name', 'like', '%' . $search . '%')
                    ->orWhereHas('schoolClass', fn($sq) => $sq->where('class_name', 'like', "%{$search}%"))
                    ->orWhereHas('schoolSection', fn($sq) => $sq->where('section_name', 'like', "%{$search}%"))
                    ->orWhereHas('schoolSession', fn($sq) => $sq->where('session_year', 'like', "%{$search}%"))
                    ->orWhereHas('schoolGroup', fn($sq) => $sq->where('group_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }
        if ($request->filled('session_id')) {
            $query->where('session_id', $request->session_id);
        }

        return $query->orderBy('id', 'desc')->paginate(10);
    }

    public function store(SchoolExamNameRequest $request)
    {
        $data = $request->validated();
        $data['school_id'] = $this->getSchoolId();

        try {
            $exam = DB::transaction(function () use ($data) {
                return SchoolExamName::create($data);
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Exam name created successfully',
                'data'    => $exam->load(['schoolClass', 'schoolGroup', 'schoolSection', 'schoolSession'])
            ], 201);
        } catch (QueryException $exception) {
            if ((string) $exception->getCode() !== '23000') {
                throw $exception;
            }

            report($exception);

            return response()->json([
                'message' => 'The exam could not be saved with the selected academic information.',
                'errors' => [
                    'exam_name' => ['An exam with this name already exists for the selected class, group, section, and session.'],
                ],
            ], 422);
        }
    }

    public function show($id)
    {
        return SchoolExamName::where('school_id', $this->getSchoolId())
            ->with(['schoolClass', 'schoolGroup', 'schoolSection', 'schoolSession'])
            ->findOrFail($id);
    }

    public function update(SchoolExamNameRequest $request, $id)
    {
        $data = $request->validated();
        $schoolId = $this->getSchoolId();

        try {
            $exam = DB::transaction(function () use ($schoolId, $id, $data) {
                $record = SchoolExamName::where('school_id', $schoolId)->findOrFail($id);
                $record->update($data);
                return $record;
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Exam name updated successfully',
                'data'    => $exam->fresh()->load(['schoolClass', 'schoolGroup', 'schoolSection', 'schoolSession'])
            ]);
        } catch (QueryException $exception) {
            if ((string) $exception->getCode() !== '23000') {
                throw $exception;
            }

            report($exception);

            return response()->json([
                'message' => 'The exam could not be updated with the selected academic information.',
                'errors' => [
                    'exam_name' => ['An exam with this name already exists for the selected class, group, section, and session.'],
                ],
            ], 422);
        }
    }

    public function export(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();

        if (!$school) {
            return response()->json(['message' => 'School not found'], 404);
        }

        $query = SchoolExamName::where('school_id', $school->id)
            ->with(['schoolClass', 'schoolGroup', 'schoolSection', 'schoolSession']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('exam_name', 'like', '%' . $search . '%')
                    ->orWhereHas('schoolClass', fn($sq) => $sq->where('class_name', 'like', "%{$search}%"))
                    ->orWhereHas('schoolSection', fn($sq) => $sq->where('section_name', 'like', "%{$search}%"))
                    ->orWhereHas('schoolGroup', fn($sq) => $sq->where('group_name', 'like', "%{$search}%"))
                    ->orWhereHas('schoolSession', fn($sq) => $sq->where('session_year', 'like', "%{$search}%"));
            });
        }

        foreach (['class_id', 'section_id', 'group_id', 'session_id'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        $records = $query->orderBy('id', 'desc')->get();
        $type = $request->query('type', 'pdf');

        if ($type === 'excel') {
            return Excel::download(new ExamNameExport($records), 'exam_names_' . now()->format('Ymd') . '.xlsx');
        }

        $pdf = Pdf::loadView('exports.exam_name_list_pdf', [
            'records' => $records,
            'school' => $school,
            'date' => now()->format('j-F-Y')
        ]);

        return $pdf->download('exam_names_' . now()->format('Ymd') . '.pdf');
    }

    public function destroy($id)
    {
        try {
            $schoolId = $this->getSchoolId();

            DB::transaction(function () use ($schoolId, $id) {
                $exam = SchoolExamName::where('school_id', $schoolId)->findOrFail($id);
                $exam->delete();
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete exam name.', 'error' => $e->getMessage()], 500);
        }
    }
}
