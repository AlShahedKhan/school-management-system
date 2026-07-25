@extends('layouts.service-details')

@section('title', 'Madrasha Management | Astha Academics')

@section('content')
    @php
        $features = [
            ['icon' => 'fa-user-graduate', 'title' => 'শিক্ষার্থী ও আবাসিক ব্যবস্থাপনা', 'description' => 'আবাসিক ও অনাবাসিক শিক্ষার্থীর ভর্তি, ব্যক্তিগত তথ্য, অভিভাবক, আবাসন এবং দৈনিক উপস্থিতি পরিচালনা করুন।'],
            ['icon' => 'fa-book-quran', 'title' => 'কিতাব ও হিফজ বিভাগ', 'description' => 'কিতাব, নাজেরা ও হিফজ বিভাগের পাঠ, সবক, আমুখতা, পুনরাবৃত্তি এবং শিক্ষার্থীর অগ্রগতি সংরক্ষণ করুন।'],
            ['icon' => 'fa-mosque', 'title' => 'মাদ্রাসা একাডেমিক কাঠামো', 'description' => 'জামাত, বিভাগ, শাখা, শিক্ষাবর্ষ, বিষয়, শিক্ষক বণ্টন এবং দৈনিক রুটিন সহজে পরিচালনা করুন।'],
            ['icon' => 'fa-file-circle-check', 'title' => 'পরীক্ষা ও মূল্যায়ন', 'description' => 'সাময়িক ও বার্ষিক পরীক্ষা, মৌখিক মূল্যায়ন, নম্বর, ফলাফল এবং শিক্ষার্থীর অগ্রগতি প্রতিবেদন তৈরি করুন।'],
            ['icon' => 'fa-hand-holding-dollar', 'title' => 'ফি, লজিং ও অনুদান', 'description' => 'মাসিক ফি, আবাসিক খরচ, খাবার বিল, বকেয়া, অনুদান এবং অন্যান্য আয়-ব্যয়ের হিসাব রাখুন।'],
            ['icon' => 'fa-bell', 'title' => 'অভিভাবক যোগাযোগ', 'description' => 'উপস্থিতি, ফলাফল, বকেয়া, ছুটি এবং গুরুত্বপূর্ণ ঘোষণা অভিভাবকের কাছে দ্রুত পৌঁছে দিন।'],
        ];
    @endphp

    <x-public.management-service-details
        title="Madrasha Management"
        description="মাদ্রাসার বিশেষ কারিকুলাম, আবাসিক ও অনাবাসিক শিক্ষার্থী, কিতাব ও হিফজ বিভাগের অগ্রগতি এবং প্রাতিষ্ঠানিক হিসাব পরিচালনার জন্য একটি সমন্বিত ডিজিটাল সমাধান।"
        icon="fa-mosque"
        icon-classes="bg-emerald-500/10 text-emerald-600"
        section-title="একটি মাদ্রাসা পরিচালনার প্রয়োজনীয় সব ফিচার"
        :features="$features"
        cta-title="আপনার মাদ্রাসাকে ডিজিটাল করতে প্রস্তুত?"
    />
@endsection
