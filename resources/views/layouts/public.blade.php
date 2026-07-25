<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $brandAssets['brandTitle'] ?? 'website')</title>
    @stack('meta')
    <link rel="icon" type="{{ $brandAssets['faviconType'] ?? 'image/png' }}" href="{{ $brandAssets['faviconUrl'] ?? asset('images/logo.png') }}">
    <link class="shortcut icon" href="{{ $brandAssets['faviconUrl'] ?? asset('images/logo.png') }}">
    <link class="apple-touch-icon" href="{{ $brandAssets['faviconUrl'] ?? asset('images/logo.png') }}">
    @stack('styles')
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen w-full overflow-x-hidden bg-[#F8F8F8] text-gray-900 flex flex-col">
    @include('partials.public.header')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('partials.public.demo-modal')

    @include('partials.public.footer')

    @stack('scripts')

</body>
</html>
