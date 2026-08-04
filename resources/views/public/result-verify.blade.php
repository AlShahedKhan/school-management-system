@extends('layouts.public')

@section('title', 'Verified Academic Result')

@section('content')
    <main class="mx-auto flex min-h-[60vh] max-w-3xl items-center justify-center px-4 py-16 sm:px-6">
        <section class="w-full border border-slate-200 bg-white p-6 text-center shadow-sm sm:p-10">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-2xl text-emerald-600">
                <i class="fas fa-check" aria-hidden="true"></i>
            </div>
            <p class="mt-5 text-[11px] font-bold uppercase tracking-[0.18em] text-emerald-600">Verified Result</p>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">{{ $school->school_name }}</h1>
            <p class="mt-2 text-sm text-slate-500">This result record was issued by the school.</p>

            <dl class="mx-auto mt-8 grid max-w-2xl grid-cols-1 border border-slate-200 text-left sm:grid-cols-2">
                <div class="border-b border-slate-200 p-4 sm:border-r"><dt class="text-xs text-slate-500">Student</dt><dd class="mt-1 font-semibold text-slate-900">{{ $result->student_name }}</dd></div>
                <div class="border-b border-slate-200 p-4"><dt class="text-xs text-slate-500">Student ID</dt><dd class="mt-1 font-semibold text-slate-900">{{ $result->student_id_number }}</dd></div>
                <div class="border-b border-slate-200 p-4 sm:border-r"><dt class="text-xs text-slate-500">Class / Group</dt><dd class="mt-1 font-semibold text-slate-900">{{ $result->class_name }} / {{ $result->group_name ?: 'N/A' }}</dd></div>
                <div class="border-b border-slate-200 p-4"><dt class="text-xs text-slate-500">Section / Session</dt><dd class="mt-1 font-semibold text-slate-900">{{ $result->section_name ?: 'N/A' }} / {{ $result->session_name }}</dd></div>
                <div class="p-4 sm:border-r"><dt class="text-xs text-slate-500">Exam</dt><dd class="mt-1 font-semibold text-slate-900">{{ $result->exam_name }}</dd></div>
                <div class="p-4"><dt class="text-xs text-slate-500">Admit Card</dt><dd class="mt-1 font-semibold text-slate-900">{{ request('admit_no') }}</dd></div>
            </dl>
        </section>
    </main>
@endsection
