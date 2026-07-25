@extends('layouts.public')

@section('title', ($brandAssets['brandTitle'] ?? 'Astha Academics') . ' | ' . $aboutPerson->localizedName())

@push('meta')
    <meta name="description" content="{{ str($aboutPerson->localizedSummary())->limit(155) }}">
@endpush

@section('content')
    <div class="public-about-shell bg-[#f7f9fc]">
        <section class="mx-auto w-full max-w-[1180px] px-4 py-12 sm:px-6 sm:py-16 lg:px-8 lg:py-20">
            <a href="{{ route('public.about.index') }}" class="public-feature-back-link">
                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M15.5 10h-11m0 0 4-4m-4 4 4 4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                {{ public_trans('public.about.back') }}
            </a>

            <div class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px] lg:items-start">
                <article class="public-about-card border border-slate-200 bg-white p-7 shadow-[0_22px_50px_rgba(15,23,42,0.06)] sm:p-9 lg:p-10">
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
                        <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-full border border-blue-100 bg-blue-50 text-2xl font-black text-blue-700 shadow-[0_10px_24px_rgba(37,99,235,0.08)]">
                            @if ($aboutPerson->image)
                                <img src="{{ asset('storage/' . $aboutPerson->image) }}" alt="{{ $aboutPerson->localizedName() }}" class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $aboutPerson->name_en), 0, 2)) ?: 'AA' }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-[13px] font-bold uppercase tracking-[0.18em] text-blue-600">{{ public_trans('public.about.people_eyebrow') }}</p>
                            <h1 class="home-title-font mt-3 text-[clamp(2rem,4vw,3.25rem)] font-black leading-tight text-slate-950">{{ $aboutPerson->localizedName() }}</h1>
                            <p class="mt-3 text-sm font-semibold uppercase tracking-[0.12em] text-blue-600">{{ $aboutPerson->localizedDesignation() }}</p>
                        </div>
                    </div>

                    <div class="mt-8 text-base leading-8 text-slate-600 sm:text-[1.05rem]">
                        @foreach (preg_split("/\r\n|\n|\r/", $aboutPerson->localizedDetails()) as $paragraph)
                            @continue(trim($paragraph) === '')
                            <p class="mt-4 first:mt-0">{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </article>

                <aside class="public-about-card border border-slate-200 bg-white p-6 shadow-[0_18px_40px_rgba(15,23,42,0.045)] lg:sticky lg:top-28">
                    <h2 class="text-lg font-bold text-slate-950">{{ $aboutPerson->localizedName() }}</h2>
                    <p class="mt-2 text-sm font-semibold uppercase tracking-[0.12em] text-blue-600">{{ $aboutPerson->localizedDesignation() }}</p>
                    <p class="mt-4 text-sm leading-7 text-slate-600">{{ $aboutPerson->localizedSummary() }}</p>
                    <a href="{{ route('public.about.index') }}" class="public-feature-card-action mt-6">
                        {{ public_trans('public.about.back') }}
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M4.5 10h11m0 0-4-4m4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </aside>
            </div>

            @if ($relatedPeople->isNotEmpty())
                <section class="mt-12">
                    <p class="text-[13px] font-bold uppercase tracking-[0.18em] text-blue-600">{{ public_trans('public.about.people_eyebrow') }}</p>
                    <h2 class="home-title-font mt-2 text-3xl font-black leading-tight text-slate-950">{{ public_trans('public.about.related_people') }}</h2>

                    <div class="mt-6 grid gap-6 md:grid-cols-3">
                        @foreach ($relatedPeople as $person)
                            <article class="public-about-card flex h-full flex-col border border-slate-200 bg-white p-6 shadow-[0_18px_40px_rgba(15,23,42,0.045)] transition-all duration-200 ease-out hover:-translate-y-1 hover:border-blue-100 hover:shadow-[0_24px_46px_rgba(15,23,42,0.075)]">
                                <h3 class="text-lg font-bold text-slate-950">{{ $person->localizedName() }}</h3>
                                <p class="mt-2 text-xs font-semibold uppercase tracking-[0.12em] text-blue-600">{{ $person->localizedDesignation() }}</p>
                                <p class="mt-4 text-sm leading-7 text-slate-600">{{ $person->localizedSummary() }}</p>
                                <div class="mt-auto pt-5">
                                    <a href="{{ route('public.about.people.show', $person) }}" class="public-feature-card-action">
                                        {{ public_trans('public.about.see_more') }}
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
