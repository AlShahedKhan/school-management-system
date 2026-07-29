<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\School;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    /**
     * Display a listing of the devices for the school.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            // Assuming Super Admin can filter by school
            if ($user->role === 'admin' && $request->filled('school_id')) {
                $schoolId = $request->input('school_id');
            } else {
                $school = School::where('user_id', $user->id)->firstOrFail();
                $schoolId = $school->id;
            }

            $query = Device::where('school_id', $schoolId);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('ip_address', 'like', "%{$search}%")
                      ->orWhere('serial_number', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%");
                });
            }

            $perPage = (int) $request->input('per_page', 30);
            $devices = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json($devices);
        } catch (\Exception $e) {
            Log::error('Device Index Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch devices.'], 500);
        }
    }

    /**
     * Store a newly created device in storage.
     */
    public function store(Request $request): JsonResponse
    {
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
            'status' => 'nullable|string|in:active,inactive,hold',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Super Admin can create for any school
            $user = Auth::user();
            if ($user->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $device = DB::transaction(function () use ($request) {
                return Device::create($request->all());
            });

            return response()->json([
                'message' => 'Device created successfully.',
                'device' => $device,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Create Device Error: ' . $e->getMessage());
            return response()->json([
                'errors' => ['exception' => [$e->getMessage()]],
                'message' => 'Failed to create device.',
            ], 422);
        }
    }

    /**
     * Display the specified device.
     */
    public function show($id): JsonResponse
    {
        try {
            $user = Auth::user();
            $query = Device::query();

            if ($user->role !== 'admin') {
                $school = School::where('user_id', $user->id)->firstOrFail();
                $query->where('school_id', $school->id);
            }

            $device = $query->findOrFail($id);

            return response()->json($device);
        } catch (\Exception $e) {
            Log::error('Show Device Error: ' . $e->getMessage());
            return response()->json(['message' => 'Device not found.'], 404);
        }
    }

    /**
     * Update the specified device in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $query = Device::query();

            if ($user->role !== 'admin') {
                 return response()->json(['message' => 'Unauthorized'], 403);
            }

            $device = $query->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'school_id' => 'required|exists:schools,id',
                'name' => 'required|string|max:255',
                'model' => 'nullable|string|max:255',
                'serial_number' => ['nullable', 'string', 'max:255', Rule::unique('devices')->ignore($device->id)],
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
                'status' => 'nullable|string|in:active,inactive,hold',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            DB::transaction(function () use ($request, $device) {
                $device->update($request->all());
            });

            return response()->json([
                'message' => 'Device updated successfully.',
                'device' => $device,
            ]);
        } catch (\Exception $e) {
            Log::error('Update Device Error: ' . $e->getMessage());
            return response()->json([
                'errors' => ['exception' => [$e->getMessage()]],
                'message' => 'Failed to update device.',
            ], 422);
        }
    }

    /**
     * Remove the specified device from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            DB::transaction(function () use ($id) {
                $device = Device::findOrFail($id);
                $device->delete();
            });

            return response()->json(['message' => 'Device deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Delete Device Error: ' . $e->getMessage());
            return response()->json([
                'errors' => ['exception' => [$e->getMessage()]],
                'message' => 'Failed to delete device.',
            ], 422);
        }
    }
}
