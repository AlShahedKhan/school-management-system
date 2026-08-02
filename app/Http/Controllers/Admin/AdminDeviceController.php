<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DeviceStatus;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\School; // <-- Import the School model
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class AdminDeviceController extends Controller
{

    public function index()
    {

        $devices = Device::with('school')->latest()->paginate(15);

        $statusCounts = Device::query()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $activeCount = $statusCounts->get(DeviceStatus::ACTIVE->value, 0);
        $inactiveCount = $statusCounts->get(DeviceStatus::INACTIVE->value, 0);
        $holdCount = $statusCounts->get(DeviceStatus::HOLD->value, 0);

        return view('admin.configuration.device.index', compact(
            'devices',
            'activeCount',
            'inactiveCount',
            'holdCount'
        ));
    }

    public function store(Request $request): JsonResponse
    {
        // Using the complete set of validation rules as you intended.
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:devices,serial_number',
            'ip_address' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'communication_type' => 'nullable|string|in:LAN,WiFi',
            'protocol' => 'nullable|string|max:255',
            'time_zone' => 'nullable|string|max:255',
            'sync_interval' => 'nullable|integer|min:1',
            'heartbeat_time' => 'nullable|integer|min:1',
            'connection_timeout' => 'nullable|integer|min:1',
            'firmware_version' => 'nullable|string|max:255',
            'device_password' => 'nullable|string|max:255',
            // Updated status rule to use Enum for better type safety.
            'status' => ['required', new Enum(DeviceStatus::class)],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $device = Device::create($request->all());
            return response()->json([
                'message' => 'Device created successfully.',
                'device' => $device,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Create Device Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create device.'], 500);
        }
    }


    /**
     * OPTIMIZED: Get device data and all necessary dropdown options in one go.
     */
    public function show(Device $device): JsonResponse
    {
        $device->load('school');
        $school = $device->school;

        $dropdowns = [
            'countries' => [],
            'divisions' => [],
            'districts' => [],
            'upazilas'  => [],
            'schools'   => [],
        ];

        if ($school) {
            // Fetch all lists needed for the dropdowns based on the device's current school
            $dropdowns['countries'] = School::distinct()->pluck('country');
            $dropdowns['divisions'] = School::where('country', $school->country)->distinct()->pluck('division');
            $dropdowns['districts'] = School::where('division', $school->division)->distinct()->pluck('district');
            $dropdowns['upazilas']  = School::where('district', $school->district)->distinct()->pluck('upazila');
            $dropdowns['schools']   = School::where('upazila', $school->upazila)->select('id', 'school_name')->get();
        }

        return response()->json([
            'device' => $device,
            'dropdowns' => $dropdowns,
        ]);
    }

    /**
     * New method: To update a device.
     */

    public function showPage(Device $device): View
    {
        $device->load('school');

        return view('admin.configuration.device.show', compact('device'));
    }


    /**
     * New method: To update a device.
     */
    public function update(Request $request, Device $device): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:devices,serial_number,' . $device->id,
            'ip_address' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'communication_type' => 'nullable|string|in:LAN,WiFi',
            'protocol' => 'nullable|string|max:255',
            'time_zone' => 'nullable|string|max:255',
            'sync_interval' => 'nullable|integer|min:1',
            'heartbeat_time' => 'nullable|integer|min:1',
            'connection_timeout' => 'nullable|integer|min:1',
            'firmware_version' => 'nullable|string|max:255',
            'device_password' => 'nullable|string|max:255',
            'status' => ['required', new Enum(DeviceStatus::class)],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $device->update($request->all());
            return response()->json([
                'message' => 'Device updated successfully.',
                'device' => $device,
            ]);
        } catch (\Exception $e) {
            Log::error('Update Device Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update device.'], 500);
        }
    }

    /**
     * New method: To delete a device.
     */
    public function destroy(Device $device): JsonResponse
    {
        try {
            $device->delete();
            return response()->json(['message' => 'Device deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Delete Device Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete device.'], 500);
        }
    }
}


