<?php

namespace App\Services;

use App\Models\SchoolFeeTemplate;
use App\Models\SchoolFeeAssign;
use Illuminate\Support\Facades\DB;
use Exception;

class SchoolFeeTemplateService
{

    public function createTemplate(array $data)
    {
        $assign = SchoolFeeAssign::findOrFail($data['fee_assign_id']);

        if ($assign->status !== 'active') {
            throw new Exception("Cannot create a template for an inactive Fee Assign.");
        }


        if ($assign->name === 'Admission') {
            $exists = SchoolFeeTemplate::where('fee_assign_id', $assign->id)
                ->where('class_id', $data['class_id'])
                ->where('session_id', $data['session_id'])
                ->exists();
            if ($exists) {
                throw new Exception("Only one Admission Fee is allowed per Class and Session.");
            }
        }


        if ($assign->name === 'Tuition') {
            $exists = SchoolFeeTemplate::where('fee_assign_id', $assign->id)
                ->where('class_id', $data['class_id'])
                ->where('session_id', $data['session_id'])
                ->exists();
            if ($exists) {
                throw new Exception("Only one Tuition Fee is allowed per Class and Session.");
            }
        }


        if ($assign->name === 'Food Fee') {
            $exists = SchoolFeeTemplate::where('fee_assign_id', $assign->id)
                ->where('session_id', $data['session_id'])
                ->where('fee_name', $data['fee_name'])
                ->exists();
            if ($exists) {
                throw new Exception("Food Name must be unique within the same session.");
            }
        }


        if ($assign->name === 'Exam Fee') {
            if (empty($data['exam_id'])) {
                throw new Exception("Exam is required to create an Exam Fee.");
            }
            $exists = SchoolFeeTemplate::where('fee_assign_id', $assign->id)
                ->where('exam_id', $data['exam_id'])
                ->exists();
            if ($exists) {
                throw new Exception("Only one Exam Fee per Exam is allowed.");
            }
        }


        if ($assign->name === 'Session Fee') {
            $exists = SchoolFeeTemplate::where('fee_assign_id', $assign->id)
                ->where('class_id', $data['class_id'])
                ->where('session_id', $data['session_id'])
                ->exists();
            if ($exists) {
                throw new Exception("Only one Session Fee is allowed per Class and Session.");
            }
        }

        return DB::transaction(function () use ($data) {
            return SchoolFeeTemplate::create($data);
        });
    }


    public function checkAdmissionTemplateExists($classId, $sessionId)
    {
        return SchoolFeeTemplate::whereHas('assign', function ($q) {
            $q->where('name', 'Admission');
        })
        ->where('class_id', $classId)
        ->where('session_id', $sessionId)
        ->exists();
    }
}
