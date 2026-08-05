<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ResultVerificationController extends Controller
{
    public function __invoke(Request $request): View
    {
        $school = School::query()->findOrFail($request->integer('school'));

        $result = DB::table('school_exam_admit_cards')
            ->where('school_id', $school->id)
            ->where('student_id_number', $request->string('student')->toString())
            ->where('admit_card_number', $request->string('admit_no')->toString())
            ->where('exam_name', $request->string('exam')->toString())
            ->where('session_name', $request->string('session')->toString())
            ->first([
                'student_id_number',
                'student_name',
                'class_name',
                'group_name',
                'section_name',
                'exam_name',
                'session_name',
            ]);

        abort_unless($result, 404);

        return view('public.result-verify', compact('school', 'result'));
    }
}
