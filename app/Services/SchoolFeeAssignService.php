<?php

namespace App\Services;

use App\Models\SchoolFeeAssign;
use Illuminate\Support\Facades\DB;

class SchoolFeeAssignService
{

    public function seedDefaultAssigns($schoolId = null)
    {
        $defaults = [
            ['name' => 'Admission', 'payment_type' => 'one_time'],
            ['name' => 'Tuition', 'payment_type' => 'monthly'],
            ['name' => 'Food Fee', 'payment_type' => 'monthly'],
            ['name' => 'Exam Fee', 'payment_type' => 'one_time'],
            ['name' => 'Fine Fee', 'payment_type' => 'one_time'],
            ['name' => 'Session Fee', 'payment_type' => 'one_time'],
        ];

        DB::transaction(function () use ($defaults, $schoolId) {
            foreach ($defaults as $default) {
                SchoolFeeAssign::firstOrCreate([
                    'school_id' => $schoolId,
                    'name' => $default['name'],
                ], [
                    'payment_type' => $default['payment_type'],
                    'is_default' => true,
                    'status' => 'active',
                ]);
            }
        });
    }


    public function toggleStatus(SchoolFeeAssign $assign, $status)
    {
        $assign->update(['status' => $status]);
    }


    public function delete(SchoolFeeAssign $assign)
    {
        if ($assign->is_default) {
            throw new \Exception("Cannot delete a default Fee Assign.");
        }

        $assign->delete();
    }
}
