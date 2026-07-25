<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DemoRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\DemoRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminDemoRequestController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $statusEnum = DemoRequestStatus::tryFrom($status);

        $demoRequests = DemoRequest::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('school_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($statusEnum !== null, fn ($query) => $query->where('status', $statusEnum->value))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $statusCounts = DemoRequest::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.demo-requests', [
            'demoRequests' => $demoRequests,
            'statuses' => DemoRequestStatus::cases(),
            'statusCounts' => $statusCounts,
            'selectedStatus' => $status,
            'search' => $search,
        ]);
    }

    public function updateStatus(Request $request, DemoRequest $demoRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(DemoRequestStatus::class)],
        ]);

        $demoRequest->update([
            'status' => DemoRequestStatus::from($validated['status']),
        ]);

        return back()->with('success', 'Demo request status updated successfully.');
    }
}
