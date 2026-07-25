@props([
    'title',
    'description',
    'icon',
    'iconClasses',
    'sectionTitle',
    'features' => [],
    'ctaTitle',
])

<section class="border-b border-slate-200 bg-white py-14 sm:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}#services" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700">
            <i class="fa-solid fa-arrow-left text-xs"></i> সার্ভিসসমূহে ফিরে যান
        </a>
        <div class="mt-8 max-w-3xl">
            <div class="flex h-14 w-14 items-center justify-center rounded-xl {{ $iconClasses }}">
                <i class="fa-solid {{ $icon }} text-2xl"></i>
            </div>
            <h1 class="mt-6 text-3xl font-extrabold text-slate-900 sm:text-4xl lg:text-5xl">{{ $title }}</h1>
            <p class="mt-5 text-base leading-8 text-slate-600 sm:text-lg">{{ $description }}</p>
        </div>
    </div>
</section>

<section class="py-14 sm:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <span class="text-sm font-bold uppercase tracking-widest text-indigo-600">মূল সুবিধাসমূহ</span>
            <h2 class="mt-3 text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ $sectionTitle }}</h2>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($features as $feature)
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-md sm:p-7">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                        <i class="fa-solid {{ $feature['icon'] }} text-xl"></i>
                    </div>
                    <h3 class="mt-5 text-lg font-bold text-slate-900">{{ $feature['title'] }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600 sm:text-base">{{ $feature['description'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="border-t border-indigo-200 bg-indigo-600 py-12 text-white">
    <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-6 px-4 sm:px-6 md:flex-row md:items-center lg:px-8">
        <div>
            <h2 class="text-2xl font-bold">{{ $ctaTitle }}</h2>
            <p class="mt-2 text-indigo-100">সিস্টেমটি আপনার প্রতিষ্ঠানে কীভাবে কাজ করবে তা জানতে একটি ডেমো বুক করুন।</p>
        </div>
        <a href="{{ route('public.demo') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-white px-6 py-3 font-bold text-indigo-600 shadow-sm transition hover:bg-indigo-50">
            ডেমো বুক করুন <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
        </a>
    </div>
</section>
