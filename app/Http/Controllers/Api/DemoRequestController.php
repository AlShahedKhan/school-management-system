<?php

namespace App\Http\Controllers\Api;

use App\Enums\DemoRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\DemoRequestStoreRequest;
use App\Models\DemoRequest;
use Illuminate\Http\JsonResponse;

class DemoRequestController extends Controller
{
    public function store(DemoRequestStoreRequest $request): JsonResponse
    {
        $demoRequest = DemoRequest::create([
            ...$request->validated(),
            'status' => DemoRequestStatus::New,
        ]);

        return response()->json([
            'message' => 'Demo request submitted successfully.',
            'data' => [
                'id' => $demoRequest->id,
            ],
        ], 201);
    }
}
