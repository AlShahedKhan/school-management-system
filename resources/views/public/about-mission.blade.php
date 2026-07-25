@extends('layouts.public')

@section('title', ($brandAssets['brandTitle'] ?? 'Astha Academics') . ' | ' . $aboutPage['mission_title'])

@push('meta')
    <meta name="description" content="{{ str($aboutPage['mission_summary'])->limit(155) }}">
@endpush

@section('content')
    <div class="public-about-shell bg-[#f7f9fc]">
        <section class="mx-auto w-full max-w-[1080px] px-4 py-12 sm:px-6 sm:py-16 lg:px-8 lg:py-20">
            <a href="{{ route('public.about.index') }}" class="public-feature-back-link">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M15.5 10h-11m0 0 4-4m-4 4 4 4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                {{ public_trans('public.about.back') }}
            </a>

            <article class="public-about-card mt-8 border border-slate-200 bg-white p-7 shadow-[0_22px_50px_rgba(15,23,42,0.06)] sm:p-9 lg:p-10">
                <p class="text-[13px] font-bold uppercase tracking-[0.18em] text-blue-600">{{ public_trans('public.about.mission_eyebrow') }}</p>
                <h1 class="home-title-font mt-4 text-[clamp(2rem,4vw,3.25rem)] font-black leading-tight text-slate-950">{{ $aboutPage['mission_title'] }}</h1>
                <p class="mt-5 max-w-[62ch] text-base leading-8 text-slate-600">{{ $aboutPage['mission_summary'] }}</p>
                <div class="mt-8 text-base leading-8 text-slate-600 sm:text-[1.05rem]">
                    @foreach (preg_split("/\r\n|\n|\r/", $aboutPage['mission_details']) as $paragraph)
                        @continue(trim($paragraph) === '')
                        <p class="mt-4 first:mt-0">{{ $paragraph }}</p>
                    @endforeach
                </div>
            </article>
        </section>
    </div>
@endsection
