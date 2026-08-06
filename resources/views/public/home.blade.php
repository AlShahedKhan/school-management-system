<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Astha Academics</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Hind Siliguri', sans-serif;
            scroll-behavior: smooth;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        .card-hover:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
    <div class="bg-indigo-600 text-white text-xs sm:text-sm py-2 px-4 text-center font-medium">
        <i class="fa-solid fa-sparkles text-amber-400 mr-2"></i> আপনার শিক্ষা প্রতিষ্ঠানকে সম্পূর্ণ অটোমেটেড করুন! ফ্রি ডেমো দেখতে আজই বুক করুন।
    </div>
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                    <i class="fa-solid fa-graduation-cap text-white text-2xl"></i>
                </div>
                <div>
                    <span class="text-xl font-bold text-slate-900 block leading-tight tracking-wide">Astha Academics</span>
                    <span class="text-xs text-indigo-600 font-semibold uppercase tracking-wider">Smart Education System</span>
                </div>
            </a>
            <nav class="hidden md:flex items-center space-x-8 text-base font-semibold text-slate-600">
                <a href="#about" class="hover:text-indigo-600 transition-colors">আমাদের লক্ষ্য</a>
                <a href="#services" class="hover:text-indigo-600 transition-colors">সার্ভিসসমূহ</a>
                <a href="#features" class="hover:text-indigo-600 transition-colors">ফিচার্স</a>
                <a href="#why-us" class="hover:text-indigo-600 transition-colors">কেন আমাদের বেছে নেবেন</a>
            </nav>
            @php
                $dashboardUrl = auth()->check() ? match (auth()->user()->role) {
                    'admin' => '/admin/dashboard',
                    'school' => '/school/dashboard',
                    'teacher' => '/teacher/dashboard',
                    'student' => '/student/dashboard',
                    default => '/login',
                } : '/login';
                $dashboardLabel = auth()->check() ? 'Dashboard' : 'Login';
            @endphp
            <div class="hidden sm:block">
                <a href="{{ url($dashboardUrl) }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-md transition-all">
                    {{ $dashboardLabel }} <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
            <button id="menu-btn" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-slate-200 px-4 pt-2 pb-6 space-y-3 shadow-lg">
            <a href="#about" class="block px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">আমাদের লক্ষ্য</a>
            <a href="#services" class="block px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">সার্ভিসসমূহ</a>
            <a href="#features" class="block px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">ফিচার্স</a>
            <a href="#why-us" class="block px-3 py-2 rounded-lg font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">কেন আমাদের বেছে নেবেন</a>
            <a href="{{ url($dashboardUrl) }}" class="block text-center px-4 py-3 rounded-lg font-bold text-white bg-indigo-600 hover:bg-indigo-700">{{ $dashboardLabel }}</a>
        </div>
    </header>
    <section class="gradient-bg text-white pt-16 pb-24 sm:pt-24 sm:pb-32 overflow-hidden relative">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#f8fafc_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        <i class="fa-solid fa-circle-check mr-2"></i> অল-ইন-ওয়ান শিক্ষা প্রতিষ্ঠান ম্যানেজমেন্ট ইআরপি
                    </span>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight tracking-normal">
                        আপনার শিক্ষা প্রতিষ্ঠানকে করুন সম্পূর্ণ <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-400">স্মার্ট ও অটোমেটেড</span>
                    </h1>
                    <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto lg:mx-0 font-normal leading-relaxed">
                        প্রশাসনিক কাজকে দ্রুত, সহজ, নির্ভুল এবং সম্পূর্ণ ডিজিটাল করার অত্যাধুনিক সফটওয়্যার। ফিঙ্গারপ্রিন্ট অ্যাটেনডেন্স, রিয়েল-টাইম এআই কল ও এসএমএস এবং অ্যাকাউন্টিং এখন এক ক্লিকেই।
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                        <a href="#services" class="w-full sm:w-auto text-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold rounded-xl shadow-xl shadow-indigo-900/30 transition-all text-base">
                            সার্ভিসসমূহ দেখুন <i class="fa-solid fa-layer-group ml-2"></i>
                        </a>
                        <a href="#contact" class="w-full sm:w-auto text-center px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl border border-white/20 backdrop-blur-sm transition-all text-base">
                            যোগাযোগ করুন <i class="fa-solid fa-phone ml-2"></i>
                        </a>
                    </div>
                </div>
                <div class="lg:col-span-5 flex justify-center relative">
                    <div class="w-full max-w-md bg-slate-800/80 rounded-2xl p-6 border border-slate-700 shadow-2xl backdrop-blur-md relative">
                        <div class="absolute -top-4 -left-4 w-12 h-12 bg-amber-500 rounded-lg flex items-center justify-center shadow-lg animate-bounce">
                            <i class="fa-solid fa-bolt text-slate-900 text-lg"></i>
                        </div>
                        <div class="flex items-center justify-between border-b border-slate-700 pb-4 mb-4">
                            <div class="flex items-center space-x-2">
                                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                                <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                            </div>
                            <span class="text-xs font-semibold text-slate-400 bg-slate-900/50 px-3 py-1 rounded-md">Live Tracking Dashboard</span>
                        </div>
                        <div class="space-y-4">
                            <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/50 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                        <i class="fa-solid fa-fingerprint text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400">রিয়েল-টাইম উপস্থিতি</p>
                                        <p class="text-sm font-bold text-white">৯৮.৫% সম্পন্ন</p>
                                    </div>
                                </div>
                                <span class="text-xs bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded-md">সক্রিয়</span>
                            </div>
                            <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/50 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-lg bg-amber-500/20 flex items-center justify-center text-amber-400">
                                        <i class="fa-solid fa-robot text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400">AI অটোমেটিক কলিং</p>
                                        <p class="text-sm font-bold text-white">স্বয়ংক্রিয় এলার্ট</p>
                                    </div>
                                </div>
                                <span class="text-xs bg-indigo-500/20 text-indigo-400 px-2 py-1 rounded-md">চলমান</span>
                            </div>
                            <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/50 flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                                        <i class="fa-solid fa-money-bill-wave text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400">আজকের মোট কালেকশন</p>
                                        <p class="text-sm font-bold text-white">৳ ৪৫,৫০০.০০</p>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chart-line text-emerald-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <span class="text-sm font-bold text-indigo-600 uppercase tracking-widest block">আমাদের পরিচিতি</span>
                <h2 class="text-3xl font-extrabold text-slate-900 sm:text-4xl">মিশন, ভিশন ও আমাদের লক্ষ্য</h2>
                <div class="w-16 h-1 bg-indigo-600 mx-auto rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-8 shadow-sm card-hover flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 mb-6">
                            <i class="fa-solid fa-bullseye text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">আমাদের মিশন</h3>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            শিক্ষা প্রতিষ্ঠানসমূহের প্রাত্যহিক এবং জটিল প্রশাসনিক কার্যাবলীকে একটি সমন্বিত ও সম্পূর্ণ স্বয়ংক্রিয় ডিজিটাল প্ল্যাটফর্মে রূপান্তর করা। প্রযুক্তির ব্যবহারের মাধ্যমে ভুলত্রুটি দূর করা এবং সকল স্তরে স্বচ্ছতা ও গতিশীলতা নিশ্চিত করাই আমাদের অন্যতম প্রধান দায়িত্ব।
                        </p>
                    </div>
                    <a href="{{ route('public.digital-transformation') }}" class="pt-6 border-t border-slate-200/60 mt-6 text-xs font-semibold text-indigo-600 uppercase tracking-wider flex items-center transition-colors hover:text-indigo-700">
                        ডিজিটাল ট্রান্সফরমেশন <i class="fa-solid fa-chevron-right ml-2"></i>
                    </a>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-8 shadow-sm card-hover flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 mb-6">
                            <i class="fa-solid fa-eye text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">আমাদের ভিশন</h3>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            বাংলাদেশের প্রতিটি স্কুল, মাদ্রাসা এবং কিন্ডারগার্টেনকে স্মার্ট ইনস্টিটিউশনে পরিণত করা। যেখানে প্রযুক্তি ও শিক্ষার মেলবন্ধনে অভিভাবক, শিক্ষক ও শিক্ষার্থীদের মধ্যে একটি রিয়েল-টাইম ও নিরবচ্ছিন্ন যোগাযোগ ব্যবস্থা গড়ে উঠবে, যা দেশের শিক্ষাব্যবস্থাকে গ্লোবাল স্ট্যান্ডার্ডে নিয়ে যাবে।
                        </p>
                    </div>
                    <a href="{{ route('public.smart-bangladesh') }}" class="pt-6 border-t border-slate-200/60 mt-6 text-xs font-semibold text-emerald-600 uppercase tracking-wider flex items-center transition-colors hover:text-emerald-700">
                        স্মার্ট বাংলাদেশ ২০৪১ <i class="fa-solid fa-chevron-right ml-2"></i>
                    </a>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-8 shadow-sm card-hover flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="w-14 h-14 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600 mb-6">
                            <i class="fa-solid fa-crosshairs text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">আমাদের লক্ষ্য</h3>
                        <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                            আপনার প্রতিষ্ঠানের প্রশাসনিক কাজকে দ্রুত, সহজ, নির্ভুল এবং সম্পূর্ণ ডিজিটাল করা, যাতে আপনি কাগজের ফাইল বা সনাতন হিসাব-নিকাশের পেছনে সময় অপচয় না করে, শিক্ষার মান উন্নয়ন এবং প্রতিষ্ঠানের ভবিষ্যৎ পরিকল্পনা বাস্তবায়নে আরও বেশি সময় ও মনোযোগ দিতে পারেন।
                        </p>
                    </div>
                    <a href="{{ route('public.full-automation') }}" class="pt-6 border-t border-slate-200/60 mt-6 text-xs font-semibold text-amber-600 uppercase tracking-wider flex items-center transition-colors hover:text-amber-700">
                        শতভাগ অটোমেশন <i class="fa-solid fa-chevron-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <section id="services" class="py-20 bg-slate-100 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <span class="text-sm font-bold text-indigo-600 uppercase tracking-widest block">টার্গেটেড সলিউশন</span>
                <h2 class="text-3xl font-extrabold text-slate-900 sm:text-4xl">যাদের জন্য আমাদের এই সার্ভিসসমূহ</h2>
                <div class="w-16 h-1 bg-indigo-600 mx-auto rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden card-hover flex flex-col justify-between">
                    <div class="p-8 space-y-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center">
                            <i class="fa-solid fa-school text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900">School Management</h3>
                        <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                            উচ্চ বিদ্যালয় এবং প্রাথমিক বিদ্যালয়গুলোর জটিল একাডেমিক স্ট্রাকচার পরিচালনা করার জন্য আদর্শ ইআরপি। ক্লাসরুম অ্যাক্টিভিটি থেকে শুরু করে পরীক্ষা ও ফলাফল প্রকাশ সব এক জায়গায়।
                        </p>
                    </div>
                    <a href="{{ route('public.school-management') }}" class="bg-slate-50 px-8 py-4 border-t border-slate-100 flex items-center justify-between text-sm font-semibold text-blue-600 transition-colors hover:bg-blue-50">
                        <span>ফিচারসমূহ দেখুন</span>
                        <i class="fa-solid fa-circle-arrow-right"></i>
                    </a>
                </div>
                <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden card-hover flex flex-col justify-between">
                    <div class="p-8 space-y-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                            <i class="fa-solid fa-mosque text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900">Madrasha Management</h3>
                        <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                            মাদ্রাসার বিশেষ কারিকুলাম, লজিং বা অনাবাসিক ছাত্র ব্যবস্থাপনা, কিতাব ও হিফজ বিভাগের বিশেষ অগ্রগতি মূল্যায়ন ট্র্যাকিং সিস্টেম সহ কাস্টমাইজড অ্যাকাউন্টিং সলিউশন।
                        </p>
                    </div>
                    <a href="{{ route('public.madrasha-management') }}" class="bg-slate-50 px-8 py-4 border-t border-slate-100 flex items-center justify-between text-sm font-semibold text-emerald-600 transition-colors hover:bg-emerald-50">
                        <span>ফিচারসমূহ দেখুন</span>
                        <i class="fa-solid fa-circle-arrow-right"></i>
                    </a>
                </div>
                <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden card-hover flex flex-col justify-between">
                    <div class="p-8 space-y-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                            <i class="fa-solid fa-children text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900">Kindergarten Management</h3>
                        <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                            ছোট বাচ্চাদের স্পেশাল কেয়ার ট্র্যাকিং, অভিভাবকদের সাথে সার্বক্ষণিক নিবিড় ডিজিটাল অটো-কমিউনিকেশন এবং সহজ ও আকর্ষণীয় ড্যাশবোর্ড ইন্টারফেস।
                        </p>
                    </div>
                    <a href="{{ route('public.kindergarten-management') }}" class="bg-slate-50 px-8 py-4 border-t border-slate-100 flex items-center justify-between text-sm font-semibold text-amber-600 transition-colors hover:bg-amber-50">
                        <span>ফিচারসমূহ দেখুন</span>
                        <i class="fa-solid fa-circle-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <span class="text-sm font-bold text-indigo-600 uppercase tracking-widest block">বিস্তারিত কার্যক্ষমতা</span>
                <h2 class="text-3xl font-extrabold text-slate-900 sm:text-4xl">সফটওয়্যারের মূল মডিউল ও ফিচারসমূহ</h2>
                <div class="w-16 h-1 bg-indigo-600 mx-auto rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 shadow-sm card-hover space-y-4">
                    <div class="w-12 h-12 bg-indigo-600 text-white rounded-xl flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-fingerprint text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">স্মার্ট Attendance System</h3>
                    <ul class="space-y-2 text-slate-600 text-sm">
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-indigo-600 mr-2 mt-1 text-xs"></i> Fingerprint Device-এর সাথে সরাসরি সংযুক্ত।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-indigo-600 mr-2 mt-1 text-xs"></i> Student In/Out রিয়েল-টাইম ট্র্যাকিং।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-indigo-600 mr-2 mt-1 text-xs"></i> Present, Absent, Late, Leave ও Holiday সম্পূর্ণ অটোমেটিক।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-indigo-600 mr-2 mt-1 text-xs"></i> কত মিনিট Late হয়েছে তাও নিখুঁতভাবে দেখা যাবে।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-indigo-600 mr-2 mt-1 text-xs"></i> Student In বা Out করার সাথে সাথে অভিভাবকের কাছে SMS এবং AI Auto Call চলে যাবে।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-indigo-600 mr-2 mt-1 text-xs"></i> Absent বা Late হলেও স্বয়ংক্রিয়ভাবে SMS ও কল পৌঁছে যাবে।</li>
                    </ul>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 shadow-sm card-hover space-y-4">
                    <div class="w-12 h-12 bg-purple-600 text-white rounded-xl flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-robot text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">AI Auto Calling System</h3>
                    <ul class="space-y-2 text-slate-600 text-sm">
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-purple-600 mr-2 mt-1 text-xs"></i> কোনো নম্বর খুঁজে আলাদা করে কল করতে হবে না।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-purple-600 mr-2 mt-1 text-xs"></i> নির্ধারিত নিয়ম অনুযায়ী সফটওয়্যার নিজেই অটো কল করবে।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-purple-600 mr-2 mt-1 text-xs"></i> Attendance, Fee Due, গুরুত্বপূর্ণ নোটিশসহ বিভিন্ন বিষয়ে স্বয়ংক্রিয় কল ও SMS পাঠানো যাবে।</li>
                    </ul>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 shadow-sm card-hover space-y-4">
                    <div class="w-12 h-12 bg-emerald-600 text-white rounded-xl flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-calculator text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">সম্পূর্ণ Financial Management</h3>
                    <ul class="space-y-2 text-slate-600 text-sm">
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-emerald-600 mr-2 mt-1 text-xs"></i> কোন ছাত্রের কোন মাসে কোন খাতে কত টাকা নির্ধারিত—সব এক জায়গায়।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-emerald-600 mr-2 mt-1 text-xs"></i> কে কত টাকা পরিশোধ করেছে তাৎক্ষণিক দেখা যাবে।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-emerald-600 mr-2 mt-1 text-xs"></i> কোন খাতে কত টাকা বকেয়া রয়েছে তা সহজেই জানা যাবে।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-emerald-600 mr-2 mt-1 text-xs"></i> নির্ধারিত তারিখের মধ্যে ফি পরিশোধ না করলে স্বয়ংক্রিয়ভাবে Reminder SMS ও AI Call চলে যাবে।</li>
                    </ul>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 shadow-sm card-hover space-y-4">
                    <div class="w-12 h-12 bg-amber-600 text-white rounded-xl flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-chart-pie text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">এক ক্লিকে প্রতিষ্ঠানের আর্থিক অবস্থা</h3>
                    <ul class="space-y-2 text-slate-600 text-sm">
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-amber-600 mr-2 mt-1 text-xs"></i> আজ মোট কত টাকা Collection হয়েছে।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-amber-600 mr-2 mt-1 text-xs"></i> মোট কত টাকা Due রয়েছে।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-amber-600 mr-2 mt-1 text-xs"></i> মোট কত টাকা Expense হয়েছে।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-amber-600 mr-2 mt-1 text-xs"></i> বর্তমানে Cash-এ কত টাকা আছে।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-amber-600 mr-2 mt-1 text-xs"></i> সব রিপোর্ট মুহূর্তেই দেখা ও প্রিন্ট করা যাবে।</li>
                    </ul>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 shadow-sm card-hover space-y-4">
                    <div class="w-12 h-12 bg-blue-600 text-white rounded-xl flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-file-invoice text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">আধুনিক Exam Management</h3>
                    <p class="text-xs font-semibold text-indigo-600 bg-indigo-50 inline-block px-2 py-1 rounded">আগে লাগত ১ সপ্তাহ, এখন মাত্র ১ ক্লিকে!</p>
                    <ul class="grid grid-cols-2 gap-2 text-slate-600 text-sm pt-1">
                        <li class="flex items-center"><i class="fa-solid fa-circle text-blue-600 mr-2 text-[8px]"></i> Admit Card</li>
                        <li class="flex items-center"><i class="fa-solid fa-circle text-blue-600 mr-2 text-[8px]"></i> Seat Plan</li>
                        <li class="flex items-center"><i class="fa-solid fa-circle text-blue-600 mr-2 text-[8px]"></i> Mark Entry</li>
                        <li class="flex items-center"><i class="fa-solid fa-circle text-blue-600 mr-2 text-[8px]"></i> Result Process</li>
                        <li class="flex items-center py-1 col-span-2"><i class="fa-solid fa-circle text-blue-600 mr-2 text-[8px]"></i> Result Publish</li>
                    </ul>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 shadow-sm card-hover space-y-4">
                    <div class="w-12 h-12 bg-rose-600 text-white rounded-xl flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-bell text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 border-b border-slate-200 pb-2">Notification System</h3>
                    <ul class="space-y-2 text-slate-600 text-sm">
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-rose-600 mr-2 mt-1 text-xs"></i> গুরুত্বপূর্ণ Notice মুহূর্তেই প্যানেলে ও মোবাইলে পাঠানো যাবে।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-rose-600 mr-2 mt-1 text-xs"></i> Holiday ঘোষণা ও ক্যালেন্ডার অটো-আপডেট।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-rose-600 mr-2 mt-1 text-xs"></i> Leave Management সহজে পরিচালনা করা যাবে।</li>
                        <li class="flex items-start"><i class="fa-solid fa-circle-check text-rose-600 mr-2 mt-1 text-xs"></i> নির্দিষ্ট শ্রেণি, শিক্ষক বা সকলের কাছে আলাদা নোটিশ পাঠানো যাবে।</li>
                    </ul>
                </div>
            </div>
            <div class="mt-16 bg-gradient-to-r from-slate-900 to-indigo-950 rounded-2xl p-8 text-white shadow-xl">
                <h4 class="text-xl sm:text-2xl font-bold text-center mb-8"><i class="fa-solid fa-users-gear mr-2 text-indigo-400"></i> ডেডিকেটেড ইউজার ড্যাশবোর্ড প্যানেল</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white/5 border border-white/10 rounded-xl p-6 backdrop-blur-sm">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-10 h-10 rounded-lg bg-indigo-500/20 text-indigo-300 flex items-center justify-center"><i class="fa-solid fa-chalkboard-user text-lg"></i></div>
                            <h5 class="text-lg font-bold text-white">Teacher Panel</h5>
                        </div>
                        <p class="text-slate-300 text-sm leading-relaxed">
                            শিক্ষকরা নিজস্ব সিকিউর লগইন থেকে রিয়েল-টাইম স্টুডেন্ট Attendance দিতে পারবেন, Class রুটিন, নোটিশ এবং পরীক্ষার মার্কস খুব সহজেই ইনপুট ও দেখতে পারবেন।
                        </p>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-xl p-6 backdrop-blur-sm">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/20 text-emerald-300 flex items-center justify-center"><i class="fa-solid fa-user-shield text-lg"></i></div>
                            <h5 class="text-lg font-bold text-white">Student & Guardian Panel</h5>
                        </div>
                        <p class="text-slate-300 text-sm leading-relaxed">
                            শিক্ষার্থী ও অভিভাবক নিজস্ব পৃথক আইডি দিয়ে লগইন করে দৈনিক উপস্থিতি (Attendance), পরীক্ষার ফলাফল (Result), মাসিক ফিসের স্টেটমেন্ট (Fee Status), নোটিশ এবং অন্যান্য একাডেমিক ট্র্যাকিং দেখতে পারবেন।
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="why-us" class="py-20 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <span class="text-sm font-bold text-indigo-600 uppercase tracking-widest block">ইউনিকনেস</span>
                <h2 class="text-3xl font-extrabold text-slate-900 sm:text-4xl">কেন আমাদের সফটওয়্যার অন্যান্যদের থেকে আলাদা?</h2>
                <div class="w-16 h-1 bg-indigo-600 mx-auto rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white border border-slate-200 rounded-xl p-6 flex items-start space-x-4">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0"><i class="fa-solid fa-clock text-lg"></i></div>
                    <div>
                        <h4 class="font-bold text-slate-900 mb-1">সময় বাঁচায়</h4>
                        <p class="text-sm text-slate-600">সবকিছু অটোমেটেড হওয়ায় ম্যানুয়াল কাজের শত শত কর্মঘণ্টা সাশ্রয় হয়।</p>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-6 flex items-start space-x-4">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0"><i class="fa-solid fa-box-archive text-lg"></i></div>
                    <div>
                        <h4 class="font-bold text-slate-900 mb-1">কাগজপত্রের ঝামেলা কমায়</h4>
                        <p class="text-sm text-slate-600">ডিজিটাল স্টোরেজ ব্যবস্থাপনার কারণে ফাইলিং এর ঝামেলা এবং খরচ একদম শূন্যে নেমে আসে।</p>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-6 flex items-start space-x-4">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0"><i class="fa-solid fa-triangle-exclamation text-lg"></i></div>
                    <div>
                        <h4 class="font-bold text-slate-900 mb-1">ভুলের সম্ভাবনা কমায়</h4>
                        <p class="text-sm text-slate-600">এআই ও নিখুঁত অ্যালগরিদম ব্যবহারের ফলে মানুষের তৈরি করা ডেটা এন্ট্রির ভুল থাকে না।</p>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-6 flex items-start space-x-4">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0"><i class="fa-solid fa-gears text-lg"></i></div>
                    <div>
                        <h4 class="font-bold text-slate-900 mb-1">সম্পূর্ণ স্বয়ংক্রিয় (Automation)</h4>
                        <p class="text-sm text-slate-600">এসএমএস এবং এআই ভয়েস কল পাঠানোর জন্য কোনো বাড়তি ক্লিকের প্রয়োজন নেই।</p>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-6 flex items-start space-x-4">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0"><i class="fa-solid fa-shield-halved text-lg"></i></div>
                    <div>
                        <h4 class="font-bold text-slate-900 mb-1">নিরাপদ তথ্য সংরক্ষণ</h4>
                        <p class="text-sm text-slate-600">ক্লাউড সার্ভার এনক্রিপশনের মাধ্যমে আপনার প্রতিষ্ঠানের শতভাগ ডেটা সম্পূর্ণ সুরক্ষিত।</p>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-6 flex items-start space-x-4">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0"><i class="fa-solid fa-globe text-lg"></i></div>
                    <div>
                        <h4 class="font-bold text-slate-900 mb-1">যেকোনো স্থান থেকে ব্যবহারযোগ্য</h4>
                        <p class="text-sm text-slate-600">সম্পূর্ণ ক্লাউড-বেসড হওয়ায় মোবাইল বা কম্পিউটার থেকে যেকোনো সময় এক্সেস সম্ভব।</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="contact" class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-indigo-50 to-slate-100 rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-sm">
            <div class="text-center max-w-2xl mx-auto mb-10 space-y-3">
                <h3 class="text-2xl sm:text-3xl font-bold text-slate-900">ফ্রি ডেমো এবং বিস্তারিত জানতে আজই বুক করুন</h3>
                <p class="text-slate-600 text-sm sm:text-base">নিচের ফরমে আপনার তথ্য দিন, আমাদের টিম আপনার সাথে সরাসরি যোগাযোগ করবে।</p>
            </div>
            <form class="space-y-6" onsubmit="event.preventDefault(); alert('ডেমো রিকোয়েস্ট সফলভাবে পাঠানো হয়েছে!');">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">আপনার নাম</label>
                        <input type="text" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white text-sm" placeholder="উদা: মোঃ মেহেদী হাসান">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">মোবাইল নম্বর</label>
                        <input type="tel" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white text-sm" placeholder="উদা: 017XXXXXXXX">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">শিক্ষা প্রতিষ্ঠানের নাম</label>
                        <input type="text" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white text-sm" placeholder="উদা: গাজীপুর মডেল স্কুল">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">প্রতিষ্ঠানের ধরন</label>
                        <select class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white text-sm">
                            <option>School Management</option>
                            <option>Madrasha Management</option>
                            <option>Kindergarten Management</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">অতিরিক্ত বার্তা বা জিজ্ঞাসা (ঐচ্ছিক)</label>
                    <textarea rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white text-sm" placeholder="আপনার কোনো নির্দিষ্ট রিকোয়ারমেন্ট থাকলে লিখতে পারেন..."></textarea>
                </div>
                <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all text-base uppercase tracking-wider">
                    সাবমিট করুন <i class="fa-solid fa-paper-plane ml-2 text-xs"></i>
                </button>
            </form>
        </div>
    </section>
    <section class="py-20 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <span class="text-sm font-bold text-indigo-600 uppercase tracking-widest block">
                    আমাদের অবস্থান
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">
                    Google Map Location
                </h2>
                <div class="w-16 h-1 bg-indigo-600 mx-auto rounded-full mt-4"></div>
                <p class="text-slate-600 mt-4">
                    নিচের ম্যাপে আমাদের অফিসের অবস্থান দেখতে পারবেন।
                </p>
            </div>
            <div class="overflow-hidden rounded-3xl shadow-xl border border-slate-200">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3644.8940069196124!2d90.43039089999999!3d23.9995197!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755db9e73e82c87%3A0x287e6ee7c6e8311!2sZamir%20Plaza!5e0!3m2!1sen!2sbd!4v1784008989885!5m2!1sen!2sbd"
                    width="100%"
                    height="500"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <div class="text-center mt-6">
                <a href="https://maps.app.goo.gl/gRzf1KgsRKH2tYBq8?g_st=aw"
                target="_blank"
                class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-md transition-all">
                    <i class="fa-solid fa-location-dot mr-2"></i>
                    Google Maps-এ খুলুন
                </a>
            </div>
        </div>
    </section>
    @include('partials.public.service-footer')
    <a href="https://wa.me/8801337225555?text=আসসালামু%20আলাইকুম,%20আমি%20Astha%20Academics%20ERP%20সম্পর্কে%20বিস্তারিত%20জানতে%20চাই।"
    target="_blank"
    aria-label="Chat on WhatsApp"
    class="fixed bottom-6 right-6 z-50 flex items-center justify-center w-16 h-16 rounded-full bg-green-500 hover:bg-green-600 text-white shadow-2xl transition-all duration-300 hover:scale-110">
        <span class="absolute -top-1 -right-1 flex h-5 w-5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-5 w-5 bg-red-500 text-white text-[10px] items-center justify-center">
                1
            </span>
        </span>
        <i class="fab fa-whatsapp text-4xl"></i>
    </a>
    <script>
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
        const links = mobileMenu.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });
    </script>
</body>
</html>
