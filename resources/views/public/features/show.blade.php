@extends('layouts.public')

@section('title', ($brandAssets['brandTitle'] ?? 'Astha Academics') . ' | ' . $feature->localizedTitle())

@push('meta')
    <meta name="description" content="{{ str($feature->localizedDescription())->limit(155) }}">
@endpush

@section('content')
    <div class="public-features-shell bg-[#f7f9fc]">
        <section class="mx-auto w-full max-w-[1180px] px-4 py-12 sm:px-6 sm:py-16 lg:px-8 lg:py-20">
            <a
                href="{{ route('public.features.index') }}"
                class="public-feature-back-link"
            >
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M15.5 10h-11m0 0 4-4m-4 4 4 4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                {{ public_trans('public.features.back') }}
            </a>

            <div class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px] lg:items-start">
                <article class="public-feature-detail-card border border-slate-200 bg-white p-7 shadow-[0_22px_50px_rgba(15,23,42,0.06)] sm:p-9 lg:p-10">
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
                        <div class="public-feature-icon flex h-16 w-16 shrink-0 items-center justify-center rounded-full border border-blue-100 bg-blue-50 text-blue-600 shadow-[0_10px_24px_rgba(37,99,235,0.08)]">
                            <div class="h-7 w-7">
                                @include('partials.public.home-icon', ['icon' => $feature->icon])
                            </div>
                        </div>

                        <div class="min-w-0">
                            <p class="text-[13px] font-bold uppercase tracking-[0.18em] text-blue-600">
                                {{ public_trans('public.features.title') }}
                            </p>
                            <h1 class="home-title-font mt-3 text-[clamp(2rem,4vw,3.25rem)] font-black leading-tight text-slate-950">
                                {{ $feature->localizedTitle() }}
                            </h1>
                        </div>
                    </div>

                    <div class="public-feature-detail-content mt-8 max-w-[74ch] text-base leading-8 text-slate-600 sm:text-[1.05rem]">
                        @foreach (preg_split("/\r\n|\n|\r/", $feature->localizedDescription()) as $paragraph)
                            @continue(trim($paragraph) === '')
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </article>

                <aside class="public-feature-detail-card border border-slate-200 bg-white p-6 shadow-[0_18px_40px_rgba(15,23,42,0.045)] lg:sticky lg:top-28">
                    <div class="public-feature-icon flex h-14 w-14 items-center justify-center rounded-full border border-blue-100 bg-blue-50 text-blue-600 shadow-[0_10px_24px_rgba(37,99,235,0.08)]">
                        <div class="h-6 w-6">
                            @include('partials.public.home-icon', ['icon' => $feature->icon])
                        </div>
                    </div>
                    <h2 class="mt-5 text-lg font-bold text-slate-950">
                        {{ $feature->localizedTitle() }}
                    </h2>
                    <p class="public-feature-card-copy mt-3 text-sm leading-7 text-slate-600">
                        {{ $feature->localizedDescription() }}
                    </p>
                    <a
                        href="{{ route('public.features.index') }}"
                        class="public-feature-card-action mt-6"
                    >
                        {{ public_trans('public.features.back') }}
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M4.5 10h11m0 0-4-4m4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </aside>
            </div>

            @if ($relatedFeatures->isNotEmpty())
                <section class="mt-12">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="text-[13px] font-bold uppercase tracking-[0.18em] text-blue-600">
                                {{ public_trans('public.features.title') }}
                            </p>
                            <h2 class="home-title-font mt-2 text-3xl font-black leading-tight text-slate-950">
                                {{ public_trans('public.features.heading') }}
                            </h2>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-6 md:grid-cols-3">
                        @foreach ($relatedFeatures as $relatedFeature)
                            <article class="public-feature-related-card public-feature-card home-feature-card border border-slate-200 bg-white p-6 shadow-[0_18px_40px_rgba(15,23,42,0.045)] transition-all duration-200 ease-out hover:-translate-y-1 hover:border-blue-100 hover:shadow-[0_24px_46px_rgba(15,23,42,0.075)]">
                                <div class="public-feature-icon flex h-12 w-12 items-center justify-center rounded-full border border-blue-100 bg-blue-50 text-blue-600 shadow-[0_10px_24px_rgba(37,99,235,0.08)]">
                                    <div class="h-5 w-5">
                                        @include('partials.public.home-icon', ['icon' => $relatedFeature->icon])
                                    </div>
                                </div>
                                <h3 class="mt-5 text-lg font-bold text-slate-950">
                                    {{ $relatedFeature->localizedTitle() }}
                                </h3>
                                <p class="public-feature-card-copy mt-3 text-sm leading-7 text-slate-600">
                                    {{ $relatedFeature->localizedDescription() }}
                                </p>
                                <div class="public-feature-card-action-wrap">
                                    <a
                                        href="{{ route('public.features.show', $relatedFeature) }}"
                                        class="public-feature-card-action"
                                    >
                                        {{ public_trans('public.features.see_more') }}
                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M4.5 10h11m0 0-4-4m4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        </section>
    </div>
@endsection
