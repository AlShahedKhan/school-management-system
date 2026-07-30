@extends('layouts.public')

@section('title', ($brandAssets['brandTitle'] ?? 'Astha Academics') . ' | ' . public_trans('public.home.title_suffix'))

@php
    $metricToneClasses = [
        'primary' => 'home-metric-card--primary',
        'success' => 'home-metric-card--success',
        'warning' => 'home-metric-card--warning',
        'info' => 'home-metric-card--info',
    ];

    $activityToneClasses = [
        'primary' => 'bg-blue-500',
        'success' => 'bg-emerald-500',
        'warning' => 'bg-amber-500',
        'info' => 'bg-cyan-500',
    ];

    $badgeClasses = [
        'success' => 'home-status-badge--success',
        'neutral' => 'home-status-badge--neutral',
        'info' => 'home-status-badge--info',
    ];

    $attendanceWidth = preg_match('/^\d+$/', $homePage['hero_attendance_value'] ?? '')
        ? $homePage['hero_attendance_value'] . '%'
        : $homePage['hero_attendance_value'] ?? '0%';

    $schoolNameForInitials = preg_replace('/[^A-Za-z0-9\s]/', '', $homePage['hero_school_name'] ?? '');
    $schoolWords = preg_split('/\s+/', trim($schoolNameForInitials), -1, PREG_SPLIT_NO_EMPTY);
    $schoolInitials = 'DM';

    if (count($schoolWords) >= 2) {
        $schoolInitials = strtoupper(substr($schoolWords[0], 0, 1) . substr($schoolWords[1], 0, 1));
    } elseif (count($schoolWords) === 1) {
        $schoolInitials = strtoupper(substr($schoolWords[0], 0, 2));
    }

    $marqueeShowcases = isset($showcases) ? $showcases->concat($showcases) : collect();
@endphp

@section('content')
    <div data-home-page class="home-page-shell overflow-hidden bg-[#f7f9fc]"
        style="
            --hero-accent-color: {{ $homePage['hero_accent_color'] }};
            --hero-accent-soft-color: {{ $homePage['hero_accent_soft_color'] }};
        ">
        <section class="home-hero-section">
            <div
                class="home-hero-grid mx-auto grid min-h-[452px] w-full max-w-[1280px] items-center gap-10 px-4 py-12 sm:px-6 sm:py-14 lg:grid-cols-[minmax(0,620px)_minmax(500px,1fr)] lg:gap-16 lg:px-[27px] lg:py-16">
                <div class="home-reveal home-hero-copy min-w-0">
                    <h1 class="home-hero-title home-title-font max-w-[760px] font-black tracking-normal text-[#111827]">
                        <span class="inline">{{ $homePage['hero_title_line_1'] }}</span>
                        <span class="inline">
                            <span class="home-hero-highlight relative isolate inline-block">
                                {{ $homePage['hero_highlight_text'] }}
                                <span
                                    class="home-hero-highlight-underline absolute inset-x-0 -bottom-[0.02em] h-[0.16em] rounded-[3px] -z-10"></span>
                            </span>
                        </span>
                        <span class="inline">{{ $homePage['hero_title_suffix'] }}</span>
                    </h1>

                    <p class="mt-5 max-w-[560px] break-words text-[18px] leading-[1.6] text-slate-600">
                        {{ $homePage['hero_description'] }}
                    </p>

                    <div class="mt-9 flex flex-wrap items-center gap-3">
                        <button type="button" data-demo-open
                            class="home-cta-primary inline-flex h-[46px] min-w-[130px] items-center justify-center rounded-none px-6 text-sm font-semibold text-white transition-all duration-200 ease-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                            {{ public_trans('public.demo.button') }}
                        </button>
                        <a href="{{ url($homePage['hero_secondary_cta_url']) }}"
                            class="home-cta-secondary inline-flex h-[46px] min-w-[86px] items-center justify-center rounded-none border border-slate-300 bg-white px-6 text-sm font-semibold text-slate-800 transition-all duration-200 ease-out hover:-translate-y-0.5 hover:border-blue-300 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                            {{ public_trans('public.nav.login') }}
                        </a>
                    </div>
                </div>

                <div class="home-reveal home-hero-panel min-w-0 lg:justify-self-end">
                    <div class="relative mx-auto w-full max-w-[576px] min-w-0">
                        <div
                            class="home-dashboard-panel min-h-[344px] w-full max-w-full overflow-hidden border border-slate-200 bg-white px-4 py-5 shadow-[0_30px_70px_rgba(15,23,42,0.07)] sm:px-7 sm:py-[26px]">
                            <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-[15px]">
                                <div>
                                    <p class="text-[12px] font-semibold text-blue-300">
                                        {{ $homePage['hero_dashboard_label'] }}</p>
                                    <p class="mt-1 text-[14px] font-bold text-slate-950">
                                        {{ $homePage['hero_school_name'] }}</p>
                                </div>
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white shadow-[0_10px_22px_rgba(37,99,235,0.3)]">
                                    {{ $schoolInitials }}
                                </div>
                            </div>

                            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                @foreach ($homePage['hero_metric_cards'] as $metric)
                                    <div
                                        class="home-metric-card {{ $metricToneClasses[$metric['tone']] ?? 'home-metric-card--primary' }} flex min-h-[70px] flex-col items-center justify-center rounded-[10px] px-3 py-3 text-center">
                                        <p class="text-[19px] font-black leading-none sm:text-[20px]">
                                            {{ $metric['value'] }}</p>
                                        <p class="mt-2 text-[10px] font-medium sm:text-[11px]">{{ $metric['label'] }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-[19px]">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-xs font-semibold text-slate-500">
                                        {{ $homePage['hero_attendance_label'] }}</p>
                                    <p class="text-xs font-semibold text-slate-700">
                                        {{ $homePage['hero_attendance_value'] }}</p>
                                </div>
                                <div class="mt-2 h-2 rounded-full bg-slate-100">
                                    <div class="home-attendance-bar h-2 rounded-full bg-blue-500"
                                        style="width: {{ $attendanceWidth }}"></div>
                                </div>
                            </div>

                            <div class="mt-[18px]">
                                <p class="text-[12px] font-semibold uppercase tracking-[0.12em] text-slate-400">
                                    {{ $homePage['hero_activity_title'] }}</p>
                                <div class="mt-4 space-y-3">
                                    @foreach ($homePage['hero_activity_items'] as $activity)
                                        <div class="grid grid-cols-[10px_minmax(0,1fr)_auto] items-center gap-3">
                                            <span
                                                class="h-2 w-2 rounded-full {{ $activityToneClasses[$activity['tone']] ?? 'bg-blue-500' }}"></span>
                                            <p class="min-w-0 truncate text-xs font-medium text-slate-700 sm:text-[13px]">
                                                {{ $activity['text'] }}</p>
                                            <p class="text-[10px] font-medium text-blue-300">{{ $activity['meta'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div
                            class="home-status-badge {{ $badgeClasses[$homePage['hero_status_badge_style']] ?? 'home-status-badge--success' }} absolute -bottom-[18px] left-4 inline-flex h-[39px] items-center gap-3 border bg-white px-4 shadow-[0_14px_24px_rgba(15,23,42,0.12)] sm:left-[-24px]">
                            <span class="h-2.5 w-2.5 rounded-full bg-current"></span>
                            <span class="text-[12px] font-bold">{{ $homePage['hero_status_badge'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="home-stats-strip bg-[#1f4eea]" aria-label="{{ public_trans('public.home.statistics_label') }}">
            <div
                class="mx-auto grid min-h-[158px] w-full max-w-[1280px] items-center gap-y-8 px-4 py-8 sm:grid-cols-2 sm:px-6 lg:grid-cols-4 lg:px-8">
                @foreach ($homePage['stats_items'] as $item)
                    <div class="home-reveal home-stat-item text-center text-white">
                        <p class="home-title-font text-[clamp(2rem,4vw,2.25rem)] font-black leading-none">
                            {{ $item['value'] }}</p>
                        <p class="mt-3 text-sm font-semibold text-blue-50">{{ $item['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section id="about"
            class="home-section-anchor mx-auto w-full max-w-[1280px] px-4 pb-16 pt-20 sm:px-6 sm:pt-24 lg:px-8 lg:pb-[4.5rem] lg:pt-28">
            <div class="home-reveal mx-auto max-w-[720px] text-center">
                <p class="text-[15px] font-bold uppercase tracking-[0.2em] text-blue-600">{{ $homePage['intro_eyebrow'] }}
                </p>
                <h2 class="home-title-font mt-4 text-[clamp(2.25rem,5vw,3.35rem)] font-black leading-tight text-[#111827]">
                    {{ $homePage['intro_title'] }}</h2>
                <p class="mx-auto mt-5 max-w-[58ch] text-base leading-8 text-slate-700">
                    {{ $homePage['intro_description'] }}
                </p>
            </div>
        </section>

        <section id="features" class="home-section-anchor mx-auto w-full max-w-[1280px] px-4 pb-20 sm:px-6 lg:px-8">
            @if ($features->isNotEmpty())
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($features as $feature)
                        <article
                            class="home-reveal public-feature-card home-feature-card flex h-full flex-col border border-slate-200 bg-white p-7 shadow-[0_18px_40px_rgba(15,23,42,0.045)] transition-all duration-200 ease-out hover:-translate-y-1 hover:border-blue-100 hover:shadow-[0_24px_46px_rgba(15,23,42,0.075)]">
                            <div
                                class="public-feature-icon flex h-14 w-14 items-center justify-center rounded-full border border-blue-100 bg-blue-50 text-blue-600 shadow-[0_10px_24px_rgba(37,99,235,0.08)]">
                                <div class="h-6 w-6">
                                    @include('partials.public.home-icon', ['icon' => $feature->icon])
                                </div>
                            </div>
                            <h3 class="mt-6 text-xl font-bold text-slate-950">{{ $feature->localizedTitle() }}</h3>
                            <p
                                class="public-feature-card-copy mt-3 text-sm leading-7 text-slate-600 sm:text-base sm:leading-8">
                                {{ $feature->localizedDescription() }}</p>
                            <div class="public-feature-card-action-wrap">
                                <a href="{{ route('public.features.show', $feature) }}" class="public-feature-card-action">
                                    {{ public_trans('public.features.see_more') }}
                                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"
                                        aria-hidden="true">
                                        <path d="M4.5 10h11m0 0-4-4m4 4-4 4" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <x-public.pricing-section :packages="$packages" />

        @if (isset($showcases) && $showcases->isNotEmpty())
            <section class="home-showcase-shell overflow-hidden py-16 sm:py-20">
                <div class="mx-auto w-full max-w-[1280px] px-4 sm:px-6 lg:px-8">
                    <div class="home-reveal mx-auto max-w-[760px] text-center">
                        <p class="text-[13px] font-bold uppercase tracking-[0.18em] text-blue-600">
                            {{ public_trans('public.showcase.eyebrow') }}
                        </p>
                        <h2 class="home-title-font mt-4 text-[clamp(2rem,4vw,3rem)] font-black leading-tight text-slate-950">
                            {{ public_trans('public.showcase.title') }}
                        </h2>
                        <p class="mx-auto mt-4 max-w-[62ch] text-base leading-8 text-slate-600">
                            {{ public_trans('public.showcase.description') }}
                        </p>
                    </div>
                </div>

                <div class="home-reveal mt-10 sm:mt-12">
                    <div class="home-showcase-marquee group relative overflow-hidden">
                        <div class="home-showcase-fade home-showcase-fade--left"></div>
                        <div class="home-showcase-fade home-showcase-fade--right"></div>

                        <div class="home-showcase-track" tabindex="0" aria-label="{{ public_trans('public.showcase.title') }}">
                            @foreach ($marqueeShowcases as $index => $showcase)
                                <article class="home-showcase-card" @if ($index >= $showcases->count()) aria-hidden="true" @endif>
                                    <button
                                        type="button"
                                        class="home-showcase-trigger"
                                        data-showcase-open
                                        data-showcase-title="{{ $showcase->localizedTitle() }}"
                                        data-showcase-image="{{ asset('storage/' . $showcase->image) }}"
                                        @if ($index >= $showcases->count()) tabindex="-1" @endif
                                    >
                                        <div class="home-showcase-image-wrap">
                                            <img
                                                src="{{ asset('storage/' . $showcase->image) }}"
                                                alt="{{ $showcase->localizedTitle() ?: public_trans('public.showcase.image_alt') }}"
                                                class="home-showcase-image"
                                            >
                                        </div>
                                        <div class="home-showcase-copy">
                                            <p class="home-showcase-title">
                                                {{ $showcase->localizedTitle() }}
                                            </p>
                                            <span class="home-showcase-link">
                                                {{ public_trans('public.showcase.view_preview') }}
                                            </span>
                                        </div>
                                    </button>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <div class="public-blog-shell bg-[#f7f9fc]">
            <section class="mx-auto w-full max-w-[1280px] px-4 py-2 sm:px-6 sm:py-16 lg:px-8 lg:py-20">
                <div class="mx-auto max-w-[720px] text-center">
                    <h1
                        class="home-title-font mt-4 text-[clamp(2.25rem,5vw,3.75rem)] font-black leading-tight text-slate-950">
                        {{ public_trans('public.blog.heading') }}
                    </h1>
                    <p class="mx-auto mt-5 max-w-[58ch] text-base leading-8 text-slate-600">
                        {{ public_trans('public.blog.intro') }}
                    </p>
                </div>

                @if ($blogs->isNotEmpty())
                    <div class="mt-12 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($blogs as $blog)
                            @php
                                $translation = $blog->translation();
                                $description = $translation?->meta_description ?: $translation?->description;
                            @endphp

                            <article
                                class="public-blog-card flex min-h-full flex-col border border-slate-200 bg-white shadow-[0_18px_40px_rgba(15,23,42,0.045)] transition-all duration-200 ease-out hover:-translate-y-1 hover:border-blue-100 hover:shadow-[0_24px_46px_rgba(15,23,42,0.075)]">
                                <a href="{{ route('public.blogs.show', $blog) }}" class="block">
                                    <div class="public-blog-image aspect-[16/10] overflow-hidden bg-slate-100">
                                        @if ($blog->image)
                                            <img src="{{ asset('storage/' . $blog->image) }}"
                                                alt="{{ $translation?->title ?? public_trans('public.blog.image_alt') }}"
                                                class="h-full w-full object-cover transition duration-300 ease-out hover:scale-[1.03]">
                                        @else
                                            <div
                                                class="flex h-full w-full items-center justify-center px-6 text-center text-sm font-semibold text-slate-400">
                                                {{ $brandAssets['brandTitle'] ?? 'Astha Academics' }}
                                            </div>
                                        @endif
                                    </div>
                                </a>

                                <div class="flex flex-1 flex-col p-6">
                                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-blue-500">
                                        {{ bn_number($blog->published_at?->translatedFormat('j-F-Y')) }}
                                    </p>
                                    <h2 class="mt-3 text-xl font-bold leading-snug text-slate-950">
                                        <a href="{{ route('public.blogs.show', $blog) }}"
                                            class="transition hover:text-blue-600">
                                            {{ $translation?->title ?? public_trans('public.blog.untitled') }}
                                        </a>
                                    </h2>
                                    <p class="mt-3 line-clamp-3 text-sm leading-7 text-slate-600">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($description ?? ''), 150) }}
                                    </p>
                                    <div class="mt-auto pt-6">
                                        <a href="{{ route('public.blogs.show', $blog) }}"
                                            class="inline-flex h-10 items-center justify-center border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                                            {{ public_trans('public.blog.see_more') }}
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div
                        class="mx-auto mt-12 max-w-[640px] border border-slate-200 bg-white p-8 text-center shadow-[0_18px_40px_rgba(15,23,42,0.045)]">
                        <h2 class="text-xl font-bold text-slate-950">{{ public_trans('public.blog.empty_title') }}</h2>
                        <p class="mt-3 text-sm leading-7 text-slate-600">
                            {{ public_trans('public.blog.empty_description') }}
                        </p>
                    </div>
                @endif
            </section>
        </div>

        <div data-showcase-modal class="showcase-modal fixed inset-0 z-[95] hidden items-center justify-center px-4 py-6"
            aria-hidden="true">
            <div class="showcase-modal-backdrop absolute inset-0 bg-slate-950/65" data-showcase-close></div>
            <div class="showcase-modal-panel relative max-h-[calc(100vh-40px)] w-full max-w-[980px] overflow-hidden border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.22)]"
                role="dialog" aria-modal="true" aria-labelledby="showcaseModalTitle">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-blue-600">
                            {{ public_trans('public.showcase.eyebrow') }}
                        </p>
                        <h2 id="showcaseModalTitle" data-showcase-modal-title class="mt-1 text-xl font-bold text-slate-950"></h2>
                    </div>
                    <button type="button" data-showcase-close
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center border border-slate-200 text-slate-500 transition hover:border-blue-200 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                        aria-label="{{ public_trans('public.showcase.modal_close') }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            aria-hidden="true">
                            <path d="M18 6 6 18M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
                <div class="showcase-modal-media-wrap bg-slate-50 p-4 sm:p-6">
                    <div class="showcase-modal-media">
                        <img data-showcase-modal-image src="" alt="" class="h-full w-full object-contain">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
