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
    /**
     * Process and store attendance data.
     *
     * @param string   $idNumber  The user ID from the device (can be string or int).
     * @param string   $timestamp The timestamp string from the device.
     * @param int|null $deviceId  The ID of the device that sent the data.
     * @return Attendance|null
     */
    public function processAttendance(string $idNumber, string $timestamp, ?int $deviceId = null): ?Attendance
    {
        $attendable = $this->findAttendable($idNumber);
        if (!$attendable) {
            Log::warning("Attendance Error: No user found with ID Number: {$idNumber}");
            return null;
        }

        // Parse the timestamp once for consistency
        $carbonTimestamp = Carbon::parse($timestamp);

        try {
            // Check for duplicates
            $existingAttendance = $attendable->attendances()
                ->where('timestamp', $carbonTimestamp)
                ->first();

            if ($existingAttendance) {
                Log::info("Duplicate attendance record skipped for user ID {$idNumber} at {$timestamp}.");
                return $existingAttendance;
            }

            // Create new record
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

    /**
     * Find the user model by their ID number.
     *
     * @param string $idNumber
     * @return Model|null
     */
    private function findAttendable(string $idNumber): ?Model
    {
        // 1. Search for a Teacher
        $user = Teacher::where('id_number', $idNumber)->first();

        // 2. If not found, search for a Student
        if (!$user) {
            $user = User::where('role', 'student')->where('id_number', $idNumber)->first();
        }

        // 3. If still not found, search for an Employee
        if (!$user) {
            $user = Employee::where('employee_no', $idNumber)->first();
        }

        return $user;
    }
}
