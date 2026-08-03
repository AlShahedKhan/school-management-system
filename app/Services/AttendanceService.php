<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AdmissionStudent;
use App\Models\Attendance;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceService
{

    public function processAttendance(int $idNumber, string $timestamp): ?Attendance
    {
        $attendable = $this->findAttendable($idNumber);

        if (!$attendable) {
            Log::warning("Attendance Error: No user found with ID Number: {$idNumber}");
            return null;
        }

        try {
            $attendance = DB::transaction(function () use ($attendable, $timestamp) {
                return $attendable->attendances()->create([
                    'timestamp' => Carbon::parse($timestamp),
                    'status' => 'present',
                    'type' => 'check_in',
                ]);
            });

            return $attendance;
        } catch (\Throwable $e) {
            Log::error("Failed to save attendance for ID Number: {$idNumber}. Error: " . $e->getMessage());
            return null;
        }
    }


    private function findAttendable(int $idNumber)
    {
        $user = Teacher::where('id_number', $idNumber)->first();

        if (!$user) {
            $user = AdmissionStudent::where('student_id_number', $idNumber)->first();
        }

        return $user;
    }
}
