<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Store attendance data from a device.
     * This endpoint is designed to be hit by the ZKTeco device.
     */
    public function store(Request $request): JsonResponse
    {
        // ZKTeco devices often send data in a non-standard format.
        // We will try to get the raw content and parse it.
        $payload = $request->getContent();
        Log::info('Attendance Payload Received: ' . $payload);

        // Basic validation for incoming data.
        // This might need adjustment based on the actual payload from the device.
        $validator = Validator::make($request->all(), [
            'id_number' => 'required|integer',
            'timestamp' => 'required|date',
        ]);

        if ($validator->fails()) {
            // Log the validation failure with the payload for debugging
            Log::warning('Invalid attendance data received.', [
                'errors' => $validator->errors(),
                'payload' => $payload
            ]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $idNumber = (int) $request->input('id_number');
            $timestamp = $request->input('timestamp');

            $attendance = $this->attendanceService->processAttendance($idNumber, $timestamp);

            if (!$attendance) {
                return response()->json(['message' => 'Failed to process attendance. User not found or error occurred.'], 400);
            }

            return response()->json([
                'message' => 'Attendance recorded successfully.',
                'attendance' => $attendance,
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Attendance Store Error: ' . $e->getMessage(), ['payload' => $payload]);
            return response()->json([
                'errors' => ['exception' => [$e->getMessage()]],
                'message' => 'An unexpected error occurred while recording attendance.',
            ], 500);
        }
    }
}
