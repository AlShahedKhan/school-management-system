@extends('layouts.service-details')

@section('title', 'Kindergarten Management | Astha Academics')

@section('content')
    @php
        $features = [
            ['icon' => 'fa-children', 'title' => 'শিশু প্রোফাইল ও যত্ন', 'description' => 'প্রতিটি শিশুর ব্যক্তিগত তথ্য, অভিভাবক, স্বাস্থ্যসংক্রান্ত নির্দেশনা এবং বিশেষ যত্নের তথ্য নিরাপদে সংরক্ষণ করুন।'],
            ['icon' => 'fa-clipboard-check', 'title' => 'উপস্থিতি ও দৈনিক কার্যক্রম', 'description' => 'দৈনিক উপস্থিতি, ক্লাস কার্যক্রম, খাবার, বিশ্রাম এবং শিশুদের অংশগ্রহণের তথ্য সহজে ট্র্যাক করুন।'],
            ['icon' => 'fa-shapes', 'title' => 'পাঠ ও বিকাশ মূল্যায়ন', 'description' => 'বয়সভিত্তিক পাঠক্রম, ভাষা, সংখ্যা, সৃজনশীলতা, আচরণ এবং শারীরিক বিকাশের অগ্রগতি মূল্যায়ন করুন।'],
            ['icon' => 'fa-comments', 'title' => 'অভিভাবক যোগাযোগ', 'description' => 'শিশুর দৈনিক অগ্রগতি, ছবি, নোটিশ, ছুটি এবং জরুরি বার্তা অভিভাবকের কাছে নিয়মিত পৌঁছে দিন।'],
            ['icon' => 'fa-money-check-dollar', 'title' => 'ফি ব্যবস্থাপনা', 'description' => 'মাসিক ফি, পরিবহন, খাবার, কার্যক্রম ফি, ছাড়, সংগ্রহ এবং বকেয়ার পূর্ণাঙ্গ হিসাব পরিচালনা করুন।'],
            ['icon' => 'fa-bus', 'title' => 'পরিবহন ও নিরাপত্তা', 'description' => 'পরিবহন তথ্য, পিকআপ ব্যক্তি, জরুরি যোগাযোগ এবং শিশু হস্তান্তরের অনুমোদিত তথ্য সংরক্ষণ করুন।'],
        ];
    @endphp

    <x-public.management-service-details
        title="Kindergarten Management"
        description="ছোট শিশুদের যত্ন, দৈনিক কার্যক্রম, বিকাশ মূল্যায়ন, অভিভাবক যোগাযোগ এবং ফি ব্যবস্থাপনার জন্য সহজ, নিরাপদ ও ব্যবহারবান্ধব একটি ডিজিটাল প্ল্যাটফর্ম।"
        icon="fa-children"
        icon-classes="bg-amber-500/10 text-amber-600"
        section-title="একটি কিন্ডারগার্টেন পরিচালনার প্রয়োজনীয় সব ফিচার"
        :features="$features"
        cta-title="আপনার কিন্ডারগার্টেনকে ডিজিটাল করতে প্রস্তুত?"
    />
@endsection
