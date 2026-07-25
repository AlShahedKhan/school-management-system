@extends('layouts.public')

@section('title', ($brandAssets['brandTitle'] ?? 'Astha Academics') . ' | ' . public_trans('public.blog.title'))

@section('content')
    <div class="public-blog-shell bg-[#f7f9fc]">
        <section class="mx-auto w-full max-w-[1280px] px-4 py-14 sm:px-6 sm:py-16 lg:px-8 lg:py-20">
            <div class="mx-auto max-w-[720px] text-center">
                <p class="text-[13px] font-bold uppercase tracking-[0.2em] text-blue-600">{{ public_trans('public.blog.title') }}</p>
                <h1 class="home-title-font mt-4 text-[clamp(2.25rem,5vw,3.75rem)] font-black leading-tight text-slate-950">
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

                        <article class="public-blog-card flex min-h-full flex-col border border-slate-200 bg-white shadow-[0_18px_40px_rgba(15,23,42,0.045)] transition-all duration-200 ease-out hover:-translate-y-1 hover:border-blue-100 hover:shadow-[0_24px_46px_rgba(15,23,42,0.075)]">
                            <a href="{{ route('public.blogs.show', $blog) }}" class="block">
                                <div class="public-blog-image aspect-[16/10] overflow-hidden bg-slate-100">
                                    @if ($blog->image)
                                        <img
                                            src="{{ asset('storage/' . $blog->image) }}"
                                            alt="{{ $translation?->title ?? public_trans('public.blog.image_alt') }}"
                                            class="h-full w-full object-cover transition duration-300 ease-out hover:scale-[1.03]"
                                        >
                                    @else
                                        <div class="flex h-full w-full items-center justify-center px-6 text-center text-sm font-semibold text-slate-400">
                                            {{ $brandAssets['brandTitle'] ?? 'Astha Academics' }}
                                        </div>
                                    @endif
                                </div>
                            </a>

                            <div class="flex flex-1 flex-col p-6">
                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-blue-500">
                                    {{ $blog->published_at?->format('d M Y') }}
                                </p>
                                <h2 class="mt-3 text-xl font-bold leading-snug text-slate-950">
                                    <a href="{{ route('public.blogs.show', $blog) }}" class="transition hover:text-blue-600">
                                        {{ $translation?->title ?? public_trans('public.blog.untitled') }}
                                    </a>
                                </h2>
                                <p class="mt-3 line-clamp-3 text-sm leading-7 text-slate-600">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($description ?? ''), 150) }}
                                </p>
                                <div class="mt-auto pt-6">
                                    <a
                                        href="{{ route('public.blogs.show', $blog) }}"
                                        class="inline-flex h-10 items-center justify-center border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:text-blue-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                    >
                                        {{ public_trans('public.blog.see_more') }}
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $blogs->links() }}
                </div>
            @else
                <div class="mx-auto mt-12 max-w-[640px] border border-slate-200 bg-white p-8 text-center shadow-[0_18px_40px_rgba(15,23,42,0.045)]">
                    <h2 class="text-xl font-bold text-slate-950">{{ public_trans('public.blog.empty_title') }}</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        {{ public_trans('public.blog.empty_description') }}
                    </p>
                </div>
            @endif
        </section>
    </div>
@endsection
