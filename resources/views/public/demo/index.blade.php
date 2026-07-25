@extends('layouts.public')

@section('title', ($brandAssets['brandTitle'] ?? 'Astha Academics') . ' | ' . public_trans('public.demo.page_title'))

@section('content')
    <div class="public-demo-shell bg-[#f7f9fc]">
        <section class="mx-auto w-full max-w-[1280px] px-4 py-14 sm:px-6 sm:py-16 lg:px-8 lg:py-20">
            <div class="mx-auto grid max-w-[1120px] gap-10 lg:grid-cols-[minmax(0,0.95fr)_minmax(420px,1fr)] lg:items-start">
                <div class="home-reveal">
                    <p class="text-[13px] font-bold uppercase tracking-[0.18em] text-blue-600">
                        {{ public_trans('public.demo.eyebrow') }}
                    </p>
                    <h1 class="home-title-font mt-4 text-[clamp(2.2rem,4.5vw,3.6rem)] font-black leading-tight text-slate-950">
                        {{ public_trans('public.demo.page_heading') }}
                    </h1>
                    <p class="mt-5 max-w-[58ch] text-base leading-8 text-slate-600">
                        {{ public_trans('public.demo.page_intro') }}
                    </p>
                    <div class="mt-8">
                        <a
                            href="{{ route('public.pricing.index') }}"
                            class="home-cta-primary inline-flex h-11 items-center justify-center px-5 text-sm font-semibold text-white transition-all duration-200 ease-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                        >
                            {{ public_trans('public.nav.pricing') }}
                        </a>
                    </div>
                </div>

                <div class="home-reveal border border-slate-200 bg-white p-5 shadow-[0_24px_52px_rgba(15,23,42,0.06)] sm:p-6">
                    <div class="mb-5 border-b border-slate-200 pb-4">
                        <h2 class="text-xl font-bold text-slate-950">{{ public_trans('public.demo.title') }}</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            {{ public_trans('public.demo.page_form_intro') }}
                        </p>
                    </div>

                    @include('partials.public.demo-form', [
                        'formMode' => 'page',
                        'formIdPrefix' => 'demo_page',
                        'formWrapperClass' => 'space-y-4',
                        'submitButtonClass' => 'home-cta-primary inline-flex h-11 items-center justify-center px-5 text-sm font-semibold text-white transition-all duration-200 ease-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2',
                    ])
                </div>
            </div>
        </section>
    </div>
@endsection
