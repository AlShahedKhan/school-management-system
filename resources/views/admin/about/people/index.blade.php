@extends('layouts.admin')

@section('title', 'About People')
@section('page-title', 'About People')

@section('content')
    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
        <div class="mb-6 flex flex-col justify-between gap-4 border border-gray-100 bg-white p-4 shadow-sm md:flex-row md:items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">About People</h2>
                <p class="text-xs text-gray-500">Manage public leadership profiles, their order, activation state, and bilingual content.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.about.settings.edit') }}" class="inline-flex items-center justify-center border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-600 transition hover:border-blue-200 hover:text-blue-600">
                    <i class="fas fa-sliders-h mr-2"></i> About Settings
                </a>
                <a href="{{ route('admin.about.people.create') }}" class="inline-flex items-center justify-center bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i> New Profile
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-5 border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden border border-gray-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-[980px] w-full border-collapse text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Profile</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Slug</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Order</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Status</th>
                            <th class="border-b border-gray-100 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($people as $person)
                            <tr class="align-top transition hover:bg-slate-50">
                                <td class="px-4 py-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-100 text-sm font-bold text-slate-600">
                                            @if ($person->image)
                                                <img src="{{ asset('storage/' . $person->image) }}" alt="{{ $person->name_en }}" class="h-full w-full object-cover">
                                            @else
                                                {{ strtoupper(substr($person->name_en, 0, 2)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $person->name_en }}</p>
                                            <p class="mt-1 text-sm text-gray-500">{{ $person->name_bn ?: 'No Bangla name' }}</p>
                                            <p class="mt-2 text-xs font-semibold uppercase tracking-[0.12em] text-gray-400">{{ $person->designation_en }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm font-medium text-gray-700">{{ $person->slug }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="text-sm font-medium text-gray-700">{{ $person->sort_order }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex border px-2.5 py-1 text-xs font-bold {{ $person->is_active ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-gray-50 text-gray-600' }}">
                                        {{ $person->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('admin.about.people.edit', $person) }}" class="inline-flex h-9 w-9 items-center justify-center border border-blue-100 bg-blue-50 text-sm text-blue-700 transition hover:border-blue-200 hover:bg-blue-100 hover:text-blue-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2" aria-label="Edit profile" title="Edit profile">
                                            <i class="fas fa-pen-to-square" aria-hidden="true"></i>
                                        </a>
                                        <form action="{{ route('admin.about.people.destroy', $person) }}" method="POST" onsubmit="return confirm('Delete this profile?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center border border-red-100 bg-red-50 text-sm text-red-700 transition hover:border-red-200 hover:bg-red-100 hover:text-red-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2" aria-label="Delete profile" title="Delete profile">
                                                <i class="fas fa-trash-can" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center">
                                    <p class="text-sm font-semibold text-gray-700">No about profiles created yet.</p>
                                    <p class="mt-1 text-xs text-gray-400">Create your first public profile for the About page.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            {{ $people->links() }}
        </div>
    </div>
@endsection
