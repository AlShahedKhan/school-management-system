@extends('layouts.public')

@php
    $translation = $blog->translation();
    $pageTitle = $translation?->meta_title ?: $translation?->title ?: public_trans('public.blog.fallback_title');
    $metaDescription = $translation?->meta_description;
    $metaKeywords = $translation?->meta_keywords;
@endphp

@section('title', $pageTitle . ' | ' . ($brandAssets['brandTitle'] ?? 'Astha Academics'))

@push('meta')
    @if ($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    @if ($metaKeywords)
        <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
@endpush

@section('content')
    <div class="public-blog-shell bg-[#f7f9fc]">
        <article class="mx-auto w-full max-w-[980px] px-4 py-12 sm:px-6 sm:py-16 lg:px-8 lg:py-20">
            <a
                href="{{ route('public.blogs.index') }}"
                class="inline-flex h-10 items-center justify-center border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
            >
                {{ public_trans('public.blog.back') }}
            </a>

            <header class="mt-8">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-600">
                    {{ $blog->published_at?->format('j-F-Y') }}
                </p>
                <h1 class="home-title-font mt-4 text-[clamp(2.5rem,6vw,4.5rem)] font-black leading-[1.05] text-slate-950">
                    {{ $translation?->title ?? public_trans('public.blog.untitled') }}
                </h1>
            </header>

            <div class="public-blog-detail-image mt-9 aspect-[16/8.5] overflow-hidden border border-slate-200 bg-slate-100">
                @if ($blog->image)
                    <img
                        src="{{ asset('storage/' . $blog->image) }}"
                        alt="{{ $translation?->title ?? public_trans('public.blog.image_alt') }}"
                        class="h-full w-full object-cover"
                    >
                @else
                    <div class="flex h-full w-full items-center justify-center px-6 text-center text-base font-semibold text-slate-400">
                        {{ $brandAssets['brandTitle'] ?? 'Astha Academics' }}
                    </div>
                @endif
            </div>

            @if ($translation?->seo_tags)
                <div class="mt-8 flex flex-wrap gap-2">
                    @foreach (array_filter(array_map('trim', explode(',', $translation->seo_tags))) as $tag)
                        <span class="public-blog-tag inline-flex border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
            @endif

            <div class="public-blog-content mt-8 whitespace-pre-line text-lg leading-9 text-slate-700">
                {{ $translation?->description ?? public_trans('public.blog.no_description') }}
            </div>
        </article>
    </div>
@endsection
