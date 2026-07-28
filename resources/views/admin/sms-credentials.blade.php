@extends('layouts.admin')
@section('title', 'SMS Credentials')
@section('page-title', 'SMS Credentials')

@section('content')
<div class="p-4 md:p-6 bg-gray-50 min-h-screen">
    <div class="bg-white shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">SMS Gateway Credentials</h2>
            <p class="text-xs text-gray-500">Configure global SMS Provider API Key, Sender ID, and Gateway Settings</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-2xl bg-white border border-gray-200 p-6">
        <form action="{{ route('admin.sms-credentials.update') }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $credential->id }}">

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">SMS Provider Name</label>
                <input type="text" name="provider_name" value="{{ old('provider_name', $credential->provider_name) }}" class="w-full text-xs p-2 border border-gray-300 focus:border-blue-600 outline-none" required placeholder="e.g. Greenweb">
                @error('provider_name') <span class="text-red-500 text-[11px]">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">API Key / Token</label>
                <input type="text" name="api_key" value="{{ old('api_key', $credential->api_key) }}" class="w-full text-xs p-2 border border-gray-300 focus:border-blue-600 outline-none" required placeholder="Enter SMS Provider API Token">
                @error('api_key') <span class="text-red-500 text-[11px]">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Sender ID / Masking (Optional)</label>
                <input type="text" name="sender_id" value="{{ old('sender_id', $credential->sender_id) }}" class="w-full text-xs p-2 border border-gray-300 focus:border-blue-600 outline-none" placeholder="e.g. ASTHA_ACADEMY">
                @error('sender_id') <span class="text-red-500 text-[11px]">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">API Endpoint URL</label>
                <input type="url" name="api_url" value="{{ old('api_url', $credential->api_url) }}" class="w-full text-xs p-2 border border-gray-300 focus:border-blue-600 outline-none" placeholder="https://api.greenweb.com.bd/api.php">
                @error('api_url') <span class="text-red-500 text-[11px]">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6 flex items-center gap-2">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $credential->is_active) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                <label for="is_active" class="text-xs font-semibold text-gray-700">Enable Global SMS Gateway</label>
            </div>

            <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-xs font-bold uppercase tracking-wider hover:bg-blue-700 transition-all">
                Save SMS Credentials
            </button>
        </form>
    </div>
</div>
@endsection
