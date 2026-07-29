@extends('layouts.school')
@section('title', 'SMS Settings')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@include('school.partials.sms-settings-modal')

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof openSmsSettingsModal === 'function') {
        openSmsSettingsModal();
    } else {
        const modal = document.getElementById('smsSettingsModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }
});
</script>
@endsection
