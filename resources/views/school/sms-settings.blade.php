@extends('layouts.school')
@section('title', 'SMS Settings')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@include('school.partials.sms-settings-modal')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('smsSettingsModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
    if (typeof window.openSmsSettingsModal === 'function') {
        window.openSmsSettingsModal();
    } else if (typeof window.loadSmsSettingsData === 'function') {
        window.loadSmsSettingsData();
    }
});
</script>
@endsection
