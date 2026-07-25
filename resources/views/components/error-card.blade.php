@props([
    'code' => '404',
    'title' => 'Page Not Found',
    'message' => 'The page you are looking for does not exist.',
    'icon' => 'mdi mdi-link-variant-off',
    'color' => 'slate',
])

@php
    $palette = match ($color) {
        'red' => ['icon' => 'text-red-600', 'badge' => 'bg-red-50 text-red-600', 'bar' => 'from-red-500 to-red-600', 'btn' => 'bg-red-600 hover:bg-red-700'],
        'amber' => ['icon' => 'text-amber-600', 'badge' => 'bg-amber-50 text-amber-600', 'bar' => 'from-amber-500 to-amber-600', 'btn' => 'bg-amber-600 hover:bg-amber-700'],
        'orange' => ['icon' => 'text-orange-600', 'badge' => 'bg-orange-50 text-orange-600', 'bar' => 'from-orange-500 to-orange-600', 'btn' => 'bg-orange-600 hover:bg-orange-700'],
        'yellow' => ['icon' => 'text-yellow-600', 'badge' => 'bg-yellow-50 text-yellow-600', 'bar' => 'from-yellow-500 to-yellow-600', 'btn' => 'bg-yellow-600 hover:bg-yellow-700'],
        'purple' => ['icon' => 'text-purple-600', 'badge' => 'bg-purple-50 text-purple-600', 'bar' => 'from-purple-500 to-purple-600', 'btn' => 'bg-purple-600 hover:bg-purple-700'],
        'slate' => ['icon' => 'text-slate-600', 'badge' => 'bg-slate-50 text-slate-600', 'bar' => 'from-slate-500 to-slate-600', 'btn' => 'bg-slate-600 hover:bg-slate-700'],
        default => ['icon' => 'text-blue-600', 'badge' => 'bg-blue-50 text-blue-600', 'bar' => 'from-blue-500 to-blue-600', 'btn' => 'bg-blue-600 hover:bg-blue-700'],
    };

    $backUrl = auth()->check() ? match (auth()->user()->role) {
        'admin' => '/admin/dashboard',
        'school' => '/school/dashboard',
        'teacher' => '/teacher/dashboard',
        'student' => '/student/dashboard',
        default => '/',
    } : '/';
    $backLabel = auth()->check() ? 'Back to Dashboard' : 'Back to Home';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error {{ $code }} — {{ $title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 antialiased">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white border border-gray-200 w-full max-w-md px-8 py-12 text-center relative shadow-sm">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r {{ $palette['bar'] }}"></div>

            <div class="w-16 h-16 bg-gray-50 border border-gray-200 flex items-center justify-center mx-auto mb-5">
                <i class="{{ $icon }} text-3xl {{ $palette['icon'] }}"></i>
            </div>

            <span class="inline-block {{ $palette['badge'] }} px-3 py-1 text-[10px] font-black uppercase tracking-[1px] mb-3">
                Error {{ $code }}
            </span>

            <h1 class="text-lg font-black text-gray-900 uppercase tracking-tight mb-2">
                {{ $title }}
            </h1>

            <p class="text-sm text-gray-500 leading-relaxed max-w-xs mx-auto mb-6">
                {{ $message }}
            </p>

            <a href="{{ $backUrl }}"
               class="inline-flex items-center gap-2 text-white px-5 py-2.5 text-xs font-bold uppercase tracking-wider transition-colors {{ $palette['btn'] }}">
                <i class="mdi mdi-home-outlined text-sm"></i>
                {{ $backLabel }}
            </a>
        </div>
    </div>
</body>
</html>
