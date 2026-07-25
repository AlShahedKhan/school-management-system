@extends('layouts.public')

@section('title', ($brandAssets['brandTitle'] ?? 'Astha Academics') . ' | ' . public_trans('public.about.title'))

@section('content')
    <div class="public-about-shell bg-[#f7f9fc]">
        <section class="mx-auto w-full max-w-[1280px] px-4 py-14 sm:px-6 sm:py-16 lg:px-8 lg:py-20">
            <div class="mx-auto max-w-[760px] text-center">
                <p class="text-[13px] font-bold uppercase tracking-[0.18em] text-blue-600">{{ $aboutPage['page_eyebrow'] }}</p>
                <h1 class="home-title-font mt-4 text-[clamp(2.3rem,5vw,4rem)] font-black leading-tight text-slate-950">
                    {{ $aboutPage['page_title'] }}
                </h1>
                <p class="mx-auto mt-5 max-w-[60ch] text-base leading-8 text-slate-600">
                    {{ $aboutPage['page_intro'] }}
                </p>
            </div>

            @if ($people->isNotEmpty())
                <section class="mt-14">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="text-[13px] font-bold uppercase tracking-[0.18em] text-blue-600">{{ public_trans('public.about.people_eyebrow') }}</p>
                            <h2 class="home-title-font mt-2 text-3xl font-black leading-tight text-slate-950">
                                {{ public_trans('public.about.people_title') }}
                            </h2>
                        </div>
                    </div>

                    <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($people as $person)
                            @php
                                $initials = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $person->name_en), 0, 2));
                            @endphp
                            <article class="public-about-card flex h-full flex-col border border-slate-200 bg-white p-7 shadow-[0_18px_40px_rgba(15,23,42,0.045)] transition-all duration-200 ease-out hover:-translate-y-1 hover:border-blue-100 hover:shadow-[0_24px_46px_rgba(15,23,42,0.075)]">
                                <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-full border border-blue-100 bg-blue-50 text-2xl font-black text-blue-700 shadow-[0_10px_24px_rgba(37,99,235,0.08)]">
                                    @if ($person->image)
                                        <img src="{{ asset('storage/' . $person->image) }}" alt="{{ $person->localizedName() }}" class="h-full w-full object-cover">
                                    @else
                                        {{ $initials !== '' ? $initials : 'AA' }}
                                    @endif
                                </div>
                                <h3 class="mt-6 text-xl font-bold text-slate-950">{{ $person->localizedName() }}</h3>
                                <p class="mt-2 text-sm font-semibold uppercase tracking-[0.12em] text-blue-600">{{ $person->localizedDesignation() }}</p>
                                <p class="mt-4 text-sm leading-7 text-slate-600">
                                    {{ $person->localizedSummary() }}
                                </p>
                                <div class="mt-auto pt-6">
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

            <section class="mt-14 grid gap-6 lg:grid-cols-2">
                @foreach (['mission' => 'mission', 'vision' => 'vision'] as $key => $routeKey)
                    <article class="public-about-card flex h-full flex-col border border-slate-200 bg-white p-7 shadow-[0_18px_40px_rgba(15,23,42,0.045)]">
                        <p class="text-[13px] font-bold uppercase tracking-[0.18em] text-blue-600">
                            {{ public_trans('public.about.' . $key . '_eyebrow') }}
                        </p>
                        <h2 class="home-title-font mt-4 text-3xl font-black leading-tight text-slate-950">
                            {{ $aboutPage[$key . '_title'] }}
                        </h2>
                        <p class="mt-4 text-base leading-8 text-slate-600">
                            {{ $aboutPage[$key . '_summary'] }}
                        </p>
                        <div class="mt-auto pt-6">
                            <a href="{{ route('public.about.' . $routeKey) }}" class="public-feature-card-action">
                                {{ public_trans('public.about.see_more') }}
                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M4.5 10h11m0 0-4-4m4 4-4 4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </section>
        </section>
    </div>
@endsection
