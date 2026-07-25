<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Astha Academics')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Hind Siliguri', sans-serif; }
    </style>
</head>
<body class="flex min-h-screen flex-col bg-slate-50 text-slate-800 antialiased">
    <div class="bg-indigo-600 px-4 py-2 text-center text-xs font-medium text-white sm:text-sm">
        <i class="fa-solid fa-sparkles mr-2 text-amber-400"></i>
        আপনার শিক্ষা প্রতিষ্ঠানকে সম্পূর্ণ অটোমেটেড করুন! ফ্রি ডেমো দেখতে আজই বুক করুন।
    </div>

    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur-md">
        <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 shadow-lg shadow-indigo-200">
                    <i class="fa-solid fa-graduation-cap text-2xl text-white"></i>
                </span>
                <span>
                    <span class="block text-xl font-bold leading-tight tracking-wide text-slate-900">Astha Academics</span>
                    <span class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Smart Education System</span>
                </span>
            </a>

            <nav class="hidden items-center gap-8 text-base font-semibold text-slate-600 md:flex">
                <a href="{{ route('home') }}#about" class="transition-colors hover:text-indigo-600">আমাদের লক্ষ্য</a>
                <a href="{{ route('home') }}#services" class="text-indigo-600">সার্ভিসসমূহ</a>
                <a href="{{ route('home') }}#features" class="transition-colors hover:text-indigo-600">ফিচার্স</a>
                <a href="{{ route('home') }}#why-us" class="transition-colors hover:text-indigo-600">কেন আমাদের বেছে নেবেন</a>
            </nav>

            <a href="{{ url('/login') }}" class="hidden items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-md transition-colors hover:bg-indigo-700 sm:inline-flex">
                Login <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </a>

            <button id="serviceMenuButton" type="button" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden" aria-expanded="false" aria-controls="serviceMobileMenu" aria-label="মেনু খুলুন">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>
        </div>

        <nav id="serviceMobileMenu" class="hidden space-y-2 border-t border-slate-200 bg-white px-4 py-4 shadow-lg md:hidden">
            <a href="{{ route('home') }}#about" class="block rounded-lg px-3 py-2 font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">আমাদের লক্ষ্য</a>
            <a href="{{ route('home') }}#services" class="block rounded-lg bg-indigo-50 px-3 py-2 font-medium text-indigo-600">সার্ভিসসমূহ</a>
            <a href="{{ route('home') }}#features" class="block rounded-lg px-3 py-2 font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">ফিচার্স</a>
            <a href="{{ route('home') }}#why-us" class="block rounded-lg px-3 py-2 font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">কেন আমাদের বেছে নেবেন</a>
            <a href="{{ url('/login') }}" class="block rounded-lg bg-indigo-600 px-4 py-3 text-center font-bold text-white sm:hidden">Login</a>
        </nav>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    @include('partials.public.service-footer')

    <script>
        const serviceMenuButton = document.getElementById('serviceMenuButton');
        const serviceMobileMenu = document.getElementById('serviceMobileMenu');

        serviceMenuButton?.addEventListener('click', () => {
            const isOpen = !serviceMobileMenu.classList.toggle('hidden');
            serviceMenuButton.setAttribute('aria-expanded', String(isOpen));
        });
    </script>
</body>
</html>
