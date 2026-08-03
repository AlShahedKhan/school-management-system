<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
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

        // Parse the timestamp once for consistency
        $carbonTimestamp = Carbon::parse($timestamp);

        try {

            $existingAttendance = $attendable->attendances()
                ->where('timestamp', $carbonTimestamp)
                ->first();

            if ($existingAttendance) {

                Log::info("Duplicate attendance record skipped for user ID {$idNumber} at {$timestamp}.");
                return $existingAttendance; // Return the existing record
            }

            $attendance = DB::transaction(function () use ($attendable, $carbonTimestamp, $deviceId) {
                return $attendable->attendances()->create([
                    'device_id' => $deviceId,
                    'timestamp' => $carbonTimestamp,
                    'status'    => 'present',
                    'type'      => 'check_in',
                    'source'    => 'device',
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
            $user = User::where('role', 'student')->where('id_number', $idNumber)->first();
        }

        if (!$user) {
            $user = Employee::where('employee_no', $idNumber)->first();
        }

        return $user;
    }
}
