@extends('layouts.admin')

@section('title', 'Demo Requests')
@section('page-title', 'Demo Requests')

@section('content')
    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
        <div
            class="mb-6 flex flex-col justify-between gap-4 border border-gray-100 bg-white p-4 shadow-sm md:flex-row md:items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Demo Requests</h2>
                <p class="text-xs text-gray-500">Review and update demo bookings submitted from the public home page.</p>
            </div>
            <a href="{{ route('admin.demo-requests.index') }}"
                class="inline-flex items-center justify-center border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-600 transition hover:border-blue-200 hover:text-blue-600">
                <i class="fas fa-rotate-right mr-2"></i> Reset Filters
            </a>
        </div>

        @if (session('success'))
            <div class="mb-5 border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
            <div class="border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-gray-400">Total</p>
                <p class="mt-2 text-2xl font-black text-gray-900">{{ $demoRequests->total() }}</p>
            </div>
            @foreach ($statuses as $status)
                <div class="border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-gray-400">{{ $status->label() }}</p>
                    <p class="mt-2 text-2xl font-black text-gray-900">{{ $statusCounts[$status->value] ?? 0 }}</p>
                </div>
            @endforeach
        </div>

        <div class="mb-6 border border-gray-100 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('admin.demo-requests.index') }}"
                class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_220px_auto]">
                <div>
                    <label for="search"
                        class="mb-1 block text-[10px] font-bold uppercase tracking-[0.12em] text-gray-500">Search</label>
                    <input id="search" name="search" value="{{ $search }}" type="search"
                        placeholder="Name, school, phone, or email"
                        class="h-11 w-full border border-gray-200 px-3 text-sm outline-none transition focus:border-blue-500">
                </div>
                <div>
                    <label for="status"
                        class="mb-1 block text-[10px] font-bold uppercase tracking-[0.12em] text-gray-500">Status</label>
                    <select id="status" name="status"
                        class="h-11 w-full border border-gray-200 bg-white px-3 text-sm outline-none transition focus:border-blue-500">
                        <option value="">All Statuses</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="inline-flex h-11 w-full items-center justify-center bg-blue-600 px-5 text-sm font-semibold text-white transition hover:bg-blue-700 lg:w-auto">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-hidden border border-gray-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-[1050px] w-full border-collapse text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th
                                class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                                Requester</th>
                            <th
                                class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                                School</th>
                            <th
                                class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                                Contact</th>
                            <th
                                class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                                Message</th>
                            <th
                                class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                                Submitted</th>
                            <th
                                class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($demoRequests as $demoRequest)
                            <tr class="align-top transition hover:bg-slate-50">
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-gray-900">{{ $demoRequest->name }}</p>
                                    <p class="mt-1 text-xs text-gray-500">#{{ $demoRequest->id }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-gray-800">{{ $demoRequest->school_name }}</p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Student Qty:
                                        <span class="font-medium text-gray-700">{{ $demoRequest->student_qty ?: '—' }}</span>
                                    </p>
                                </td>
                                <td class="px-4 py-4">
                                    <a href="tel:{{ $demoRequest->phone }}"
                                        class="block text-sm font-semibold text-blue-600 hover:text-blue-700">
                                        {{ $demoRequest->phone }}
                                    </a>
                                    @unless (blank($demoRequest->email))
                                        <a href="mailto:{{ $demoRequest->email }}"
                                            class="mt-1 block text-xs text-gray-500 hover:text-blue-600">
                                            {{ $demoRequest->email }}
                                        </a>
                                    @endunless
                                    @empty($demoRequest->email)
                                        <p class="mt-1 text-xs text-gray-400">No email</p>
                                    @endempty
                                </td>
                                <td class="max-w-[280px] px-4 py-4">
                                    @unless (blank($demoRequest->message))
                                        <details class="group">
                                            <summary
                                                class="cursor-pointer text-sm font-semibold text-gray-700 transition hover:text-blue-600">
                                                View message
                                            </summary>
                                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-600">
                                                {{ $demoRequest->message }}</p>
                                        </details>
                                    @endunless
                                    @empty($demoRequest->message)
                                        <span class="text-sm text-gray-400">No message</span>
                                    @endempty
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm font-semibold text-gray-700">
                                        {{ $demoRequest->created_at?->format('d M Y') }}</p>
                                    <p class="mt-1 text-xs text-gray-400">{{ $demoRequest->created_at?->format('h:i A') }}
                                    </p>
                                    <p class="mt-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">
                                        Booking
                                    </p>
                                    <p class="mt-1 text-xs text-gray-600">
                                        {{ $demoRequest->booking_date?->format('d M Y') ?: 'No date' }}
                                        @if ($demoRequest->booking_time)
                                            <span class="text-gray-400">at</span>
                                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $demoRequest->booking_time)->format('h:i A') }}
                                        @elseif ($demoRequest->booking_date)
                                            <span class="text-gray-400">at No time</span>
                                        @endif
                                    </p>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="w-[210px] rounded-lg border border-slate-200 bg-slate-50/70 p-3">
                                        <div class="mb-3 flex items-center justify-between gap-3">
                                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500">
                                                Current Status
                                            </p>
                                            <span
                                                class="inline-flex min-w-[88px] items-center justify-center border px-2.5 py-1 text-xs font-bold {{ $demoRequest->status->badgeClass() }}">
                                                {{ $demoRequest->status->label() }}
                                            </span>
                                        </div>
                                    <form method="POST"
                                        action="{{ route('admin.demo-requests.update-status', $demoRequest) }}"
                                        class="grid gap-2 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-end">
                                        @csrf
                                        @method('PATCH')
                                        <div class="min-w-0">
                                            <label for="status_{{ $demoRequest->id }}"
                                                class="mb-1 block text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500">
                                                Change To
                                            </label>
                                            <select id="status_{{ $demoRequest->id }}" name="status"
                                                class="h-10 w-full border border-slate-200 bg-white px-3 text-sm outline-none transition focus:border-blue-500">
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status->value }}" @selected($demoRequest->status === $status)>
                                                        {{ $status->label() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit"
                                            class="h-10 whitespace-nowrap bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-blue-600">
                                            Save
                                        </button>
                                    </form>
                                    @error('status')
                                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <p class="text-sm font-semibold text-gray-700">No demo requests found.</p>
                                    <p class="mt-1 text-xs text-gray-400">New public demo bookings will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            {{ $demoRequests->links() }}
        </div>
    </div>
@endsection
