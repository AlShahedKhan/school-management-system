@extends('layouts.public')

@section('title', ($brandAssets['brandTitle'] ?? 'Astha Academics') . ' | ' . public_trans('public.features.title'))

@section('content')
    <div class="public-features-shell bg-[#f7f9fc]">
        <section class="mx-auto w-full max-w-[1280px] px-4 py-14 sm:px-6 sm:py-16 lg:px-8 lg:py-20">
            <div class="mx-auto max-w-[720px] text-center">
                <p class="text-[13px] font-bold uppercase tracking-[0.2em] text-blue-600">{{ public_trans('public.features.title') }}</p>
                <h1 class="home-title-font mt-4 text-[clamp(2.25rem,5vw,3.75rem)] font-black leading-tight text-slate-950">
                    {{ public_trans('public.features.heading') }}
                </h1>
                <p class="mx-auto mt-5 max-w-[58ch] text-base leading-8 text-slate-600">
                    {{ public_trans('public.features.intro') }}
                </p>
            </div>

            @if ($features->isNotEmpty())
                <div class="mt-12 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($features as $feature)
                        <article class="public-feature-card home-feature-card flex h-full flex-col border border-slate-200 bg-white p-7 shadow-[0_18px_40px_rgba(15,23,42,0.045)] transition-all duration-200 ease-out hover:-translate-y-1 hover:border-blue-100 hover:shadow-[0_24px_46px_rgba(15,23,42,0.075)]">
                            <div class="public-feature-icon flex h-14 w-14 items-center justify-center rounded-full border border-blue-100 bg-blue-50 text-blue-600 shadow-[0_10px_24px_rgba(37,99,235,0.08)]">
                                <div class="h-6 w-6">
                                    @include('partials.public.home-icon', ['icon' => $feature->icon])
                                </div>
                            </div>
                            <h2 class="mt-6 text-xl font-bold text-slate-950">{{ $feature->localizedTitle() }}</h2>
                            <p class="public-feature-card-copy mt-3 text-sm leading-7 text-slate-600 sm:text-base sm:leading-8">
                                {{ $feature->localizedDescription() }}
                            </p>
                            <div class="public-feature-card-action-wrap">
                                <a
                                    href="{{ route('public.features.show', $feature) }}"
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

                @if ($features->hasPages())
                    <div class="public-pagination mt-10 flex justify-center">
                        {{ $features->onEachSide(1)->links() }}
                    </div>
                @endif
            @else
                <div class="mx-auto mt-12 max-w-[640px] border border-slate-200 bg-white p-8 text-center shadow-[0_18px_40px_rgba(15,23,42,0.045)]">
                    <h2 class="text-xl font-bold text-slate-950">{{ public_trans('public.features.empty_title') }}</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        {{ public_trans('public.features.empty_description') }}
                    </p>
                </div>
            @endif
        </section>
    </div>
@endsection
