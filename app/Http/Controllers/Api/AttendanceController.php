<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Store attendance data from a ZKTeco device.
     * This endpoint is designed to handle multiple data formats from devices.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $payload = $request->getContent();
        parse_str($payload, $data);


        $serialNumber = $request->query('SN') ?? $data['SN'] ?? null;

        if (!$serialNumber) {
            Log::channel('attendance')->error('Request received without a device serial number (SN) in query or body.');
            return response("ERROR: SN NOT FOUND", 400);
        }

        $device = Device::where('serial_number', $serialNumber)->first();

        if (!$device) {
            Log::channel('attendance')->error("Device with SN '{$serialNumber}' not found in the database.");
            return response("ERROR: DEVICE NOT REGISTERED", 404);
        }

        $recordsProcessed = 0;


        if (isset($data['USERID']) && isset($data['CHECKTIME'])) {
            Log::channel('attendance')->info("Processing as 'key=value' format for SN: {$serialNumber}");

            $idNumber = $data['USERID'];
            $timestamp = $data['CHECKTIME'];

            $this->processRecord($idNumber, $timestamp, $device->id);
            $recordsProcessed++;

        } else {

            Log::channel('attendance')->info("Processing as 'tab-separated' format for SN: {$serialNumber}");
            $lines = explode("\n", $payload);

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                $parts = explode("\t", $line);
                if (count($parts) < 2) {
                    Log::channel('attendance')->warning("Skipping malformed line: " . $line);
                    continue;
                }

                $idNumber = $parts[0];
                $timestamp = $parts[1];

                $this->processRecord($idNumber, $timestamp, $device->id);
                $recordsProcessed++;
            }
        }

        Log::channel('attendance')->info("Successfully processed {$recordsProcessed} records for device SN '{$serialNumber}'.");

        // The device expects an "OK" response
        return response("OK", 200);
    }

    /**
     * A helper method to call the attendance service.
     * It handles both integer and string PINs.
     */
    private function processRecord(string $idNumber, string $timestamp, int $deviceId): void
    {
        $this->attendanceService->processAttendance($idNumber, $timestamp, $deviceId);
    }


    /**
     * Handle heartbeat pings from a ZKTeco device.
     * This method updates the device's 'last_heartbeat_at' timestamp.
     *
     * @param Request $request
     * @return Response
     */
    public function handleHeartbeat(Request $request): Response
    {
        $serialNumber = $request->query('SN');
        if (!$serialNumber) {
            Log::channel('attendance')->warning('Heartbeat received without a serial number (SN).');
            return response("ERROR", 400);
        }

        $device = Device::where('serial_number', $serialNumber)->first();

        if ($device) {
            $device->last_heartbeat_at = now();
            $device->save();
            Log::channel('attendance')->info("Heartbeat received from device SN: {$serialNumber}");
        } else {
            Log::channel('attendance')->error("Heartbeat from unregistered device SN: {$serialNumber}");
        }

        return response("OK", 200);
    }
}


