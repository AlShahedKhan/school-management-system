<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $blogs = [
            [
                'slug' => 'school-management-made-simple',
                'image' => 'blogs/seeded/blog-1.jpg',
                'is_featured' => true,
                'translations' => [
                    'en' => [
                        'title' => 'School Management Made Simple',
                        'description' => 'A modern school platform should reduce daily administrative work, not add more friction. This guide explains how centralized student records, communication tools, and reporting can help schools move faster with more confidence.',
                        'seo_tags' => 'school management, education software, school operations',
                        'meta_title' => 'School Management Made Simple | Astha Academics',
                        'meta_keywords' => 'school management software, school operations platform, education management',
                        'meta_description' => 'See how a unified platform can simplify school management and reduce manual work for your team.',
                    ],
                    'bn' => [
                        'title' => 'স্কুল ম্যানেজমেন্ট এখন আরও সহজ',
                        'description' => 'একটি আধুনিক স্কুল প্ল্যাটফর্মের কাজ হওয়া উচিত দৈনন্দিন প্রশাসনিক চাপ কমানো। এই লেখায় দেখানো হয়েছে কীভাবে কেন্দ্রীভূত শিক্ষার্থী তথ্য, যোগাযোগ ব্যবস্থা এবং রিপোর্টিং টুল স্কুল পরিচালনা সহজ করে।',
                        'seo_tags' => 'স্কুল ম্যানেজমেন্ট, শিক্ষা সফটওয়্যার, স্কুল অপারেশন',
                        'meta_title' => 'স্কুল ম্যানেজমেন্ট এখন আরও সহজ | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'স্কুল ম্যানেজমেন্ট সফটওয়্যার, শিক্ষা ব্যবস্থাপনা, স্কুল অপারেশন প্ল্যাটফর্ম',
                        'meta_description' => 'একটি একীভূত প্ল্যাটফর্ম কীভাবে স্কুল পরিচালনা সহজ করে তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'why-parent-communication-matters',
                'image' => 'blogs/seeded/blog-2.jpg',
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => 'Why Parent Communication Builds Trust',
                        'description' => 'Consistent communication helps families feel connected to classroom progress and school decisions. Learn how structured notices, attendance alerts, and fee reminders improve trust without overwhelming your staff.',
                        'seo_tags' => 'parent communication, school trust, guardian engagement',
                        'meta_title' => 'Why Parent Communication Builds Trust | Astha Academics',
                        'meta_keywords' => 'parent communication tools, guardian engagement, school messaging',
                        'meta_description' => 'Learn why clear parent communication strengthens trust and improves school operations.',
                    ],
                    'bn' => [
                        'title' => 'অভিভাবকের সাথে যোগাযোগ কেন আস্থা তৈরি করে',
                        'description' => 'নিয়মিত যোগাযোগ পরিবারকে বিদ্যালয়ের অগ্রগতি ও সিদ্ধান্তের সঙ্গে যুক্ত রাখে। নোটিশ, উপস্থিতি সতর্কতা এবং ফি রিমাইন্ডার কীভাবে আস্থা বাড়ায় তা এখানে আলোচনা করা হয়েছে।',
                        'seo_tags' => 'অভিভাবক যোগাযোগ, স্কুল আস্থা, গার্ডিয়ান এনগেজমেন্ট',
                        'meta_title' => 'অভিভাবকের সাথে যোগাযোগ কেন আস্থা তৈরি করে | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'অভিভাবক যোগাযোগ টুল, গার্ডিয়ান এনগেজমেন্ট, স্কুল মেসেজিং',
                        'meta_description' => 'পরিষ্কার অভিভাবক যোগাযোগ কীভাবে আস্থা তৈরি করে তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'digital-admissions-without-chaos',
                'image' => 'blogs/seeded/blog-3.jpg',
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => 'Digital Admissions Without Chaos',
                        'description' => 'Admissions season often brings scattered forms, manual verification, and delayed follow-up. A digital admissions workflow keeps every applicant step visible from inquiry to final enrollment.',
                        'seo_tags' => 'digital admissions, school enrollment, admission workflow',
                        'meta_title' => 'Digital Admissions Without Chaos | Astha Academics',
                        'meta_keywords' => 'digital admissions system, school enrollment software, admission tracking',
                        'meta_description' => 'Explore how digital admissions can streamline school enrollment and reduce manual follow-up.',
                    ],
                    'bn' => [
                        'title' => 'ঝামেলা ছাড়া ডিজিটাল ভর্তি প্রক্রিয়া',
                        'description' => 'ভর্তি মৌসুমে ছড়িয়ে থাকা ফর্ম, ম্যানুয়াল যাচাই এবং দেরিতে ফলো-আপ বড় সমস্যা হয়ে দাঁড়ায়। একটি ডিজিটাল ভর্তি ব্যবস্থা অনুসন্ধান থেকে চূড়ান্ত ভর্তি পর্যন্ত সবকিছু দৃশ্যমান রাখে।',
                        'seo_tags' => 'ডিজিটাল ভর্তি, স্কুল এনরোলমেন্ট, ভর্তি ওয়ার্কফ্লো',
                        'meta_title' => 'ঝামেলা ছাড়া ডিজিটাল ভর্তি প্রক্রিয়া | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'ডিজিটাল ভর্তি সিস্টেম, স্কুল ভর্তি সফটওয়্যার, ভর্তি ট্র্যাকিং',
                        'meta_description' => 'ডিজিটাল ভর্তি কীভাবে স্কুল এনরোলমেন্ট সহজ করে তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'attendance-tracking-that-actually-helps',
                'image' => 'blogs/seeded/blog-4.jpg',
                'is_featured' => true,
                'translations' => [
                    'en' => [
                        'title' => 'Attendance Tracking That Actually Helps',
                        'description' => 'Attendance should do more than store numbers. When schools can spot trends, alert guardians quickly, and review class-level patterns, attendance data becomes a practical decision tool.',
                        'seo_tags' => 'attendance tracking, school attendance, attendance alerts',
                        'meta_title' => 'Attendance Tracking That Actually Helps | Astha Academics',
                        'meta_keywords' => 'attendance software, school attendance reporting, absence alerts',
                        'meta_description' => 'See how stronger attendance tracking helps schools respond faster and make better decisions.',
                    ],
                    'bn' => [
                        'title' => 'যে উপস্থিতি ট্র্যাকিং সত্যিই কাজে লাগে',
                        'description' => 'উপস্থিতি শুধু সংখ্যা সংরক্ষণ করলেই যথেষ্ট নয়। ট্রেন্ড দেখা, দ্রুত গার্ডিয়ানকে জানানো এবং শ্রেণিভিত্তিক বিশ্লেষণ করতে পারলে উপস্থিতি তথ্য বাস্তব সিদ্ধান্তে সহায়তা করে।',
                        'seo_tags' => 'উপস্থিতি ট্র্যাকিং, স্কুল উপস্থিতি, অনুপস্থিতি সতর্কতা',
                        'meta_title' => 'যে উপস্থিতি ট্র্যাকিং সত্যিই কাজে লাগে | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'উপস্থিতি সফটওয়্যার, স্কুল উপস্থিতি রিপোর্ট, অনুপস্থিতি অ্যালার্ট',
                        'meta_description' => 'শক্তিশালী উপস্থিতি ট্র্যাকিং কীভাবে দ্রুত সিদ্ধান্তে সহায়তা করে তা দেখুন।',
                    ],
                ],
            ],
            [
                'slug' => 'smarter-fee-collection-for-busy-schools',
                'image' => 'blogs/seeded/blog-5.jpg',
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => 'Smarter Fee Collection for Busy Schools',
                        'description' => 'Fee collection becomes easier when billing, due tracking, and reminders live in one system. Schools can spend less time reconciling spreadsheets and more time serving families.',
                        'seo_tags' => 'fee collection, school billing, payment reminders',
                        'meta_title' => 'Smarter Fee Collection for Busy Schools | Astha Academics',
                        'meta_keywords' => 'school billing software, fee collection system, payment reminders',
                        'meta_description' => 'Learn how one billing workflow can simplify fee collection for school teams.',
                    ],
                    'bn' => [
                        'title' => 'ব্যস্ত স্কুলের জন্য আরও স্মার্ট ফি সংগ্রহ',
                        'description' => 'বিলিং, বকেয়া ট্র্যাকিং এবং রিমাইন্ডার এক সিস্টেমে থাকলে ফি সংগ্রহ অনেক সহজ হয়। এতে স্কুল টিম কম সময়ে বেশি নির্ভুল কাজ করতে পারে।',
                        'seo_tags' => 'ফি সংগ্রহ, স্কুল বিলিং, পেমেন্ট রিমাইন্ডার',
                        'meta_title' => 'ব্যস্ত স্কুলের জন্য আরও স্মার্ট ফি সংগ্রহ | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'স্কুল বিলিং সফটওয়্যার, ফি সংগ্রহ সিস্টেম, পেমেন্ট রিমাইন্ডার',
                        'meta_description' => 'একটি বিলিং ওয়ার্কফ্লো কীভাবে ফি সংগ্রহ সহজ করে তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'report-cards-with-less-manual-work',
                'image' => 'blogs/seeded/blog-6.jpg',
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => 'Report Cards With Less Manual Work',
                        'description' => 'Result publishing can become stressful when marks, formatting, and approvals are all handled manually. Digital result workflows reduce repetitive effort and improve accuracy.',
                        'seo_tags' => 'report cards, result publishing, academic records',
                        'meta_title' => 'Report Cards With Less Manual Work | Astha Academics',
                        'meta_keywords' => 'digital report card, exam result system, school marksheet software',
                        'meta_description' => 'Discover how digital result workflows make report card preparation easier and more accurate.',
                    ],
                    'bn' => [
                        'title' => 'কম ম্যানুয়াল কাজেই রিপোর্ট কার্ড প্রস্তুত',
                        'description' => 'নম্বর, ফরম্যাটিং এবং অনুমোদন সবকিছু হাতে করতে হলে ফলাফল প্রকাশ জটিল হয়ে যায়। ডিজিটাল রেজাল্ট ওয়ার্কফ্লো পুনরাবৃত্ত কাজ কমিয়ে নির্ভুলতা বাড়ায়।',
                        'seo_tags' => 'রিপোর্ট কার্ড, ফলাফল প্রকাশ, একাডেমিক রেকর্ড',
                        'meta_title' => 'কম ম্যানুয়াল কাজেই রিপোর্ট কার্ড প্রস্তুত | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'ডিজিটাল রিপোর্ট কার্ড, পরীক্ষা ফলাফল সিস্টেম, নম্বরপত্র সফটওয়্যার',
                        'meta_description' => 'ডিজিটাল ফলাফল ওয়ার্কফ্লো কীভাবে রিপোর্ট কার্ড তৈরি সহজ করে তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'teacher-collaboration-needs-better-tools',
                'image' => 'blogs/seeded/blog-7.jpg',
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => 'Teacher Collaboration Needs Better Tools',
                        'description' => 'Schools run better when class teachers, subject teachers, and administrators work from the same information. Shared tools reduce duplicated effort and improve coordination.',
                        'seo_tags' => 'teacher collaboration, school coordination, academic planning',
                        'meta_title' => 'Teacher Collaboration Needs Better Tools | Astha Academics',
                        'meta_keywords' => 'teacher collaboration software, school coordination tools, academic planning system',
                        'meta_description' => 'See how shared systems improve collaboration across teachers and administrators.',
                    ],
                    'bn' => [
                        'title' => 'শিক্ষক সমন্বয়ের জন্য ভালো টুল দরকার',
                        'description' => 'শ্রেণি শিক্ষক, বিষয় শিক্ষক এবং প্রশাসন একই তথ্য নিয়ে কাজ করলে বিদ্যালয় পরিচালনা আরও মসৃণ হয়। শেয়ার করা টুল পুনরাবৃত্ত কাজ কমায় এবং সমন্বয় বাড়ায়।',
                        'seo_tags' => 'শিক্ষক সমন্বয়, স্কুল কোঅর্ডিনেশন, একাডেমিক পরিকল্পনা',
                        'meta_title' => 'শিক্ষক সমন্বয়ের জন্য ভালো টুল দরকার | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'শিক্ষক সহযোগিতা সফটওয়্যার, স্কুল সমন্বয় টুল, একাডেমিক পরিকল্পনা সিস্টেম',
                        'meta_description' => 'শেয়ার করা সিস্টেম কীভাবে শিক্ষক ও প্রশাসনের সমন্বয় বাড়ায় তা দেখুন।',
                    ],
                ],
            ],
            [
                'slug' => 'class-routines-with-fewer-conflicts',
                'image' => 'blogs/seeded/blog-8.jpg',
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => 'Class Routines With Fewer Conflicts',
                        'description' => 'Routine planning becomes much easier when teacher load, room allocation, and class schedules are managed in one place. Better visibility means fewer surprises during the week.',
                        'seo_tags' => 'class routine, timetable planning, school schedule',
                        'meta_title' => 'Class Routines With Fewer Conflicts | Astha Academics',
                        'meta_keywords' => 'school timetable software, class routine planner, schedule coordination',
                        'meta_description' => 'Learn how one planning workflow can reduce conflicts in class routines and timetables.',
                    ],
                    'bn' => [
                        'title' => 'কম সংঘর্ষে ক্লাস রুটিন পরিকল্পনা',
                        'description' => 'শিক্ষকের লোড, কক্ষ বরাদ্দ এবং ক্লাস সময়সূচি এক জায়গায় থাকলে রুটিন তৈরি অনেক সহজ হয়। ভালো ভিজিবিলিটি সাপ্তাহিক সমস্যা কমিয়ে আনে।',
                        'seo_tags' => 'ক্লাস রুটিন, টাইমটেবিল পরিকল্পনা, স্কুল সময়সূচি',
                        'meta_title' => 'কম সংঘর্ষে ক্লাস রুটিন পরিকল্পনা | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'স্কুল টাইমটেবিল সফটওয়্যার, ক্লাস রুটিন প্ল্যানার, সময়সূচি সমন্বয়',
                        'meta_description' => 'একটি পরিকল্পনা ওয়ার্কফ্লো কীভাবে ক্লাস রুটিনে সংঘর্ষ কমায় তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'better-exam-preparation-for-every-term',
                'image' => 'blogs/seeded/blog-9.jpg',
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => 'Better Exam Preparation for Every Term',
                        'description' => 'Exam operations involve schedules, seat plans, marks entry, and publishing. A connected workflow helps schools prepare each term with less confusion and more accountability.',
                        'seo_tags' => 'exam preparation, seat plan, marks entry',
                        'meta_title' => 'Better Exam Preparation for Every Term | Astha Academics',
                        'meta_keywords' => 'exam management software, seat plan system, marks entry workflow',
                        'meta_description' => 'Find out how connected exam workflows reduce confusion and save time for school teams.',
                    ],
                    'bn' => [
                        'title' => 'প্রতি টার্মে আরও ভালো পরীক্ষা প্রস্তুতি',
                        'description' => 'পরীক্ষা পরিচালনায় সময়সূচি, সিট প্ল্যান, নম্বর এন্ট্রি এবং ফলাফল প্রকাশ সবই গুরুত্বপূর্ণ। সংযুক্ত ওয়ার্কফ্লো প্রতিটি টার্মকে আরও সুশৃঙ্খল করে।',
                        'seo_tags' => 'পরীক্ষা প্রস্তুতি, সিট প্ল্যান, নম্বর এন্ট্রি',
                        'meta_title' => 'প্রতি টার্মে আরও ভালো পরীক্ষা প্রস্তুতি | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'পরীক্ষা ব্যবস্থাপনা সফটওয়্যার, সিট প্ল্যান সিস্টেম, নম্বর এন্ট্রি ওয়ার্কফ্লো',
                        'meta_description' => 'সংযুক্ত পরীক্ষা ওয়ার্কফ্লো কীভাবে সময় বাঁচায় তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'school-notices-should-not-get-lost',
                'image' => 'blogs/seeded/blog-10.jpg',
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => 'School Notices Should Not Get Lost',
                        'description' => 'Important updates are only useful when people actually receive them. Centralized notice publishing helps schools reach the right audience quickly and keep communication traceable.',
                        'seo_tags' => 'school notices, announcements, communication',
                        'meta_title' => 'School Notices Should Not Get Lost | Astha Academics',
                        'meta_keywords' => 'school announcements, notice management, school communication tools',
                        'meta_description' => 'Discover how centralized notices make school communication more reliable and organized.',
                    ],
                    'bn' => [
                        'title' => 'স্কুল নোটিশ হারিয়ে যাওয়া উচিত নয়',
                        'description' => 'গুরুত্বপূর্ণ আপডেট তখনই কার্যকর হয় যখন সঠিক মানুষ তা পায়। কেন্দ্রীয় নোটিশ প্রকাশ ব্যবস্থা দ্রুত যোগাযোগ ও ট্র্যাকিং নিশ্চিত করে।',
                        'seo_tags' => 'স্কুল নোটিশ, ঘোষণা, যোগাযোগ',
                        'meta_title' => 'স্কুল নোটিশ হারিয়ে যাওয়া উচিত নয় | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'স্কুল ঘোষণা, নোটিশ ম্যানেজমেন্ট, স্কুল যোগাযোগ টুল',
                        'meta_description' => 'কেন্দ্রীয় নোটিশ কীভাবে যোগাযোগকে আরও নির্ভরযোগ্য করে তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'campus-safety-needs-fast-information',
                'image' => 'blogs/seeded/blog-11.jpg',
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => 'Campus Safety Needs Fast Information',
                        'description' => 'Safety is stronger when schools can react quickly with accurate student, guardian, and attendance information. Operational speed matters just as much as policy.',
                        'seo_tags' => 'campus safety, school operations, emergency response',
                        'meta_title' => 'Campus Safety Needs Fast Information | Astha Academics',
                        'meta_keywords' => 'campus safety system, school emergency communication, student records access',
                        'meta_description' => 'See why timely information is essential for campus safety and school response workflows.',
                    ],
                    'bn' => [
                        'title' => 'ক্যাম্পাস নিরাপত্তায় দ্রুত তথ্য জরুরি',
                        'description' => 'সঠিক শিক্ষার্থী, গার্ডিয়ান এবং উপস্থিতির তথ্য দ্রুত পাওয়া গেলে নিরাপত্তা ব্যবস্থা আরও কার্যকর হয়। নীতিমালার পাশাপাশি অপারেশনাল গতিও গুরুত্বপূর্ণ।',
                        'seo_tags' => 'ক্যাম্পাস নিরাপত্তা, স্কুল অপারেশন, জরুরি সাড়া',
                        'meta_title' => 'ক্যাম্পাস নিরাপত্তায় দ্রুত তথ্য জরুরি | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'ক্যাম্পাস সেফটি সিস্টেম, জরুরি স্কুল যোগাযোগ, শিক্ষার্থী রেকর্ড',
                        'meta_description' => 'সময়মতো তথ্য কীভাবে ক্যাম্পাস নিরাপত্তায় সহায়তা করে তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'homework-tracking-for-better-follow-up',
                'image' => 'blogs/seeded/blog-12.jpg',
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => 'Homework Tracking for Better Follow-Up',
                        'description' => 'Assignments are easier to manage when teachers, students, and families can all follow the same progress trail. Structured homework tracking improves accountability.',
                        'seo_tags' => 'homework tracking, student progress, assignment management',
                        'meta_title' => 'Homework Tracking for Better Follow-Up | Astha Academics',
                        'meta_keywords' => 'assignment tracking, student homework system, school follow-up tools',
                        'meta_description' => 'Learn how structured homework tracking helps schools improve accountability and follow-up.',
                    ],
                    'bn' => [
                        'title' => 'ভালো ফলো-আপের জন্য হোমওয়ার্ক ট্র্যাকিং',
                        'description' => 'শিক্ষক, শিক্ষার্থী এবং পরিবার একই অগ্রগতি দেখতে পারলে অ্যাসাইনমেন্ট ব্যবস্থাপনা সহজ হয়। গঠিত হোমওয়ার্ক ট্র্যাকিং জবাবদিহিতা বাড়ায়।',
                        'seo_tags' => 'হোমওয়ার্ক ট্র্যাকিং, শিক্ষার্থী অগ্রগতি, অ্যাসাইনমেন্ট ম্যানেজমেন্ট',
                        'meta_title' => 'ভালো ফলো-আপের জন্য হোমওয়ার্ক ট্র্যাকিং | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'অ্যাসাইনমেন্ট ট্র্যাকিং, হোমওয়ার্ক সিস্টেম, স্কুল ফলো-আপ টুল',
                        'meta_description' => 'গঠিত হোমওয়ার্ক ট্র্যাকিং কীভাবে জবাবদিহিতা বাড়ায় তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'smart-reminders-reduce-late-payments',
                'image' => 'blogs/seeded/blog-13.jpg',
                'is_featured' => true,
                'translations' => [
                    'en' => [
                        'title' => 'Smart Reminders Reduce Late Payments',
                        'description' => 'Schools should not have to chase every payment manually. Smart reminders help families stay informed while giving finance teams a cleaner collection workflow.',
                        'seo_tags' => 'payment reminders, late fees, school finance',
                        'meta_title' => 'Smart Reminders Reduce Late Payments | Astha Academics',
                        'meta_keywords' => 'fee reminders, school finance workflow, payment follow-up system',
                        'meta_description' => 'See how automated reminders can reduce late payments and simplify school finance operations.',
                    ],
                    'bn' => [
                        'title' => 'স্মার্ট রিমাইন্ডারে দেরি কমে পেমেন্টে',
                        'description' => 'প্রতিটি পেমেন্টের জন্য আলাদা করে ফলো-আপ করা উচিত নয়। স্মার্ট রিমাইন্ডার পরিবারকে সময়মতো জানায় এবং ফাইন্যান্স টিমকে পরিষ্কার ওয়ার্কফ্লো দেয়।',
                        'seo_tags' => 'পেমেন্ট রিমাইন্ডার, বকেয়া ফি, স্কুল ফাইন্যান্স',
                        'meta_title' => 'স্মার্ট রিমাইন্ডারে দেরি কমে পেমেন্টে | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'ফি রিমাইন্ডার, স্কুল ফাইন্যান্স ওয়ার্কফ্লো, পেমেন্ট ফলো-আপ',
                        'meta_description' => 'অটোমেটেড রিমাইন্ডার কীভাবে বকেয়া কমায় তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'how-schools-can-fix-admission-follow-up',
                'image' => 'blogs/seeded/blog-14.jpg',
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => 'How Schools Can Fix Admission Follow-Up',
                        'description' => 'Lead tracking matters in education too. When inquiries, visits, document collection, and enrollment decisions are recorded clearly, schools miss fewer good applicants.',
                        'seo_tags' => 'admission follow-up, inquiry tracking, school leads',
                        'meta_title' => 'How Schools Can Fix Admission Follow-Up | Astha Academics',
                        'meta_keywords' => 'admission follow-up system, inquiry tracking, school enrollment leads',
                        'meta_description' => 'Learn how schools can improve admission follow-up and reduce missed enrollment opportunities.',
                    ],
                    'bn' => [
                        'title' => 'স্কুল কীভাবে ভর্তি ফলো-আপ ঠিক করতে পারে',
                        'description' => 'শিক্ষা প্রতিষ্ঠানেও লিড ট্র্যাকিং গুরুত্বপূর্ণ। ইনকোয়ারি, ভিজিট, ডকুমেন্ট সংগ্রহ এবং ভর্তি সিদ্ধান্ত পরিষ্কারভাবে থাকলে ভালো আবেদনকারী হারিয়ে যায় না।',
                        'seo_tags' => 'ভর্তি ফলো-আপ, ইনকোয়ারি ট্র্যাকিং, স্কুল লিড',
                        'meta_title' => 'স্কুল কীভাবে ভর্তি ফলো-আপ ঠিক করতে পারে | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'ভর্তি ফলো-আপ সিস্টেম, ইনকোয়ারি ট্র্যাকিং, স্কুল এনরোলমেন্ট লিড',
                        'meta_description' => 'ভর্তি ফলো-আপ কীভাবে আরও কার্যকর করা যায় তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'library-automation-for-modern-campuses',
                'image' => 'blogs/seeded/blog-15.jpg',
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => 'Library Automation for Modern Campuses',
                        'description' => 'Libraries work best when records, issue history, and availability are easy to review. Digital library workflows reduce manual logs and improve day-to-day service.',
                        'seo_tags' => 'library automation, school library, book issue tracking',
                        'meta_title' => 'Library Automation for Modern Campuses | Astha Academics',
                        'meta_keywords' => 'library management software, book issue tracking, school library automation',
                        'meta_description' => 'Find out how digital library workflows improve service and reduce manual record keeping.',
                    ],
                    'bn' => [
                        'title' => 'আধুনিক ক্যাম্পাসের জন্য লাইব্রেরি অটোমেশন',
                        'description' => 'রেকর্ড, ইস্যু হিস্ট্রি এবং বইয়ের প্রাপ্যতা সহজে দেখা গেলে লাইব্রেরি পরিচালনা অনেক ভালো হয়। ডিজিটাল লাইব্রেরি ওয়ার্কফ্লো ম্যানুয়াল খাতা কমায়।',
                        'seo_tags' => 'লাইব্রেরি অটোমেশন, স্কুল লাইব্রেরি, বই ইস্যু ট্র্যাকিং',
                        'meta_title' => 'আধুনিক ক্যাম্পাসের জন্য লাইব্রেরি অটোমেশন | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'লাইব্রেরি ম্যানেজমেন্ট সফটওয়্যার, বই ইস্যু ট্র্যাকিং, স্কুল লাইব্রেরি অটোমেশন',
                        'meta_description' => 'ডিজিটাল লাইব্রেরি ওয়ার্কফ্লো কীভাবে সেবা উন্নত করে তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'transport-coordination-with-fewer-calls',
                'image' => 'blogs/seeded/blog-16.jpg',
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => 'Transport Coordination With Fewer Calls',
                        'description' => 'Transport teams need clear route, student, and guardian information to work efficiently. Better coordination reduces repeated calls and daily confusion.',
                        'seo_tags' => 'school transport, route coordination, guardian updates',
                        'meta_title' => 'Transport Coordination With Fewer Calls | Astha Academics',
                        'meta_keywords' => 'school transport management, route coordination, bus communication',
                        'meta_description' => 'See how better transport coordination can reduce manual calls and improve daily operations.',
                    ],
                    'bn' => [
                        'title' => 'কম ফোনকলেই পরিবহন সমন্বয়',
                        'description' => 'রুট, শিক্ষার্থী এবং গার্ডিয়ান তথ্য স্পষ্ট থাকলে পরিবহন টিম আরও দক্ষভাবে কাজ করতে পারে। ভালো সমন্বয় দৈনন্দিন বিভ্রান্তি কমিয়ে আনে।',
                        'seo_tags' => 'স্কুল পরিবহন, রুট সমন্বয়, গার্ডিয়ান আপডেট',
                        'meta_title' => 'কম ফোনকলেই পরিবহন সমন্বয় | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'স্কুল ট্রান্সপোর্ট ম্যানেজমেন্ট, রুট কোঅর্ডিনেশন, বাস যোগাযোগ',
                        'meta_description' => 'ভালো পরিবহন সমন্বয় কীভাবে দৈনন্দিন কাজ সহজ করে তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'school-hr-workflows-need-structure',
                'image' => 'blogs/seeded/blog-17.jpg',
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => 'School HR Workflows Need Structure',
                        'description' => 'Teacher files, staff records, leave tracking, and approvals should not live across disconnected spreadsheets. Structured HR workflows reduce mistakes and save time.',
                        'seo_tags' => 'school hr, staff records, leave tracking',
                        'meta_title' => 'School HR Workflows Need Structure | Astha Academics',
                        'meta_keywords' => 'school HR software, staff record management, leave approval workflow',
                        'meta_description' => 'Learn how structured HR workflows help schools manage staff records and approvals more efficiently.',
                    ],
                    'bn' => [
                        'title' => 'স্কুল এইচআর ওয়ার্কফ্লোতে কাঠামো দরকার',
                        'description' => 'শিক্ষক ফাইল, স্টাফ রেকর্ড, ছুটি ট্র্যাকিং এবং অনুমোদন বিচ্ছিন্ন স্প্রেডশিটে থাকলে ভুল বাড়ে। গঠিত এইচআর ওয়ার্কফ্লো সময় বাঁচায়।',
                        'seo_tags' => 'স্কুল এইচআর, স্টাফ রেকর্ড, ছুটি ট্র্যাকিং',
                        'meta_title' => 'স্কুল এইচআর ওয়ার্কফ্লোতে কাঠামো দরকার | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'স্কুল এইচআর সফটওয়্যার, স্টাফ রেকর্ড ম্যানেজমেন্ট, ছুটি অনুমোদন',
                        'meta_description' => 'গঠিত এইচআর ওয়ার্কফ্লো কীভাবে স্কুলকে সহায়তা করে তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'classroom-analytics-for-better-decisions',
                'image' => 'blogs/seeded/blog-18.jpg',
                'is_featured' => true,
                'translations' => [
                    'en' => [
                        'title' => 'Classroom Analytics for Better Decisions',
                        'description' => 'Data becomes useful when it helps schools respond early. Classroom-level analytics can highlight attendance changes, result patterns, and performance gaps before they grow.',
                        'seo_tags' => 'classroom analytics, school data, performance tracking',
                        'meta_title' => 'Classroom Analytics for Better Decisions | Astha Academics',
                        'meta_keywords' => 'school analytics, classroom performance data, academic tracking',
                        'meta_description' => 'Discover how classroom analytics support earlier, better-informed school decisions.',
                    ],
                    'bn' => [
                        'title' => 'ভালো সিদ্ধান্তের জন্য ক্লাসরুম অ্যানালিটিক্স',
                        'description' => 'তথ্য তখনই কার্যকর যখন তা সময়মতো সিদ্ধান্তে সাহায্য করে। ক্লাসভিত্তিক অ্যানালিটিক্স উপস্থিতি, ফলাফল এবং পারফরম্যান্সের পরিবর্তন আগে থেকেই দেখাতে পারে।',
                        'seo_tags' => 'ক্লাসরুম অ্যানালিটিক্স, স্কুল ডাটা, পারফরম্যান্স ট্র্যাকিং',
                        'meta_title' => 'ভালো সিদ্ধান্তের জন্য ক্লাসরুম অ্যানালিটিক্স | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'স্কুল অ্যানালিটিক্স, ক্লাসরুম পারফরম্যান্স ডাটা, একাডেমিক ট্র্যাকিং',
                        'meta_description' => 'ক্লাসরুম অ্যানালিটিক্স কীভাবে দ্রুত সিদ্ধান্তে সহায়তা করে তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'guardian-engagement-beyond-report-cards',
                'image' => 'blogs/seeded/blog-19.jpg',
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => 'Guardian Engagement Beyond Report Cards',
                        'description' => 'Families want meaningful visibility, not only end-of-term results. Ongoing engagement through updates, reminders, and student progress creates stronger school relationships.',
                        'seo_tags' => 'guardian engagement, school updates, student progress',
                        'meta_title' => 'Guardian Engagement Beyond Report Cards | Astha Academics',
                        'meta_keywords' => 'guardian engagement tools, parent updates, student progress communication',
                        'meta_description' => 'See how consistent guardian engagement creates stronger school-family relationships.',
                    ],
                    'bn' => [
                        'title' => 'রিপোর্ট কার্ডের বাইরে গার্ডিয়ান এনগেজমেন্ট',
                        'description' => 'পরিবার শুধু টার্ম শেষে ফলাফল নয়, নিয়মিত অগ্রগতিও জানতে চায়। আপডেট, রিমাইন্ডার এবং শিক্ষার্থীর অগ্রগতি শেয়ার করলে সম্পর্ক আরও শক্তিশালী হয়।',
                        'seo_tags' => 'গার্ডিয়ান এনগেজমেন্ট, স্কুল আপডেট, শিক্ষার্থী অগ্রগতি',
                        'meta_title' => 'রিপোর্ট কার্ডের বাইরে গার্ডিয়ান এনগেজমেন্ট | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'গার্ডিয়ান এনগেজমেন্ট টুল, অভিভাবক আপডেট, শিক্ষার্থী অগ্রগতি যোগাযোগ',
                        'meta_description' => 'নিয়মিত গার্ডিয়ান এনগেজমেন্ট কীভাবে সম্পর্ক শক্তিশালী করে তা জানুন।',
                    ],
                ],
            ],
            [
                'slug' => 'year-end-reporting-with-less-panic',
                'image' => 'blogs/seeded/blog-20.jpg',
                'is_featured' => false,
                'translations' => [
                    'en' => [
                        'title' => 'Year-End Reporting With Less Panic',
                        'description' => 'When records are organized throughout the year, final reporting becomes easier to prepare and verify. Schools can close the academic year with less stress and better confidence.',
                        'seo_tags' => 'year end reporting, academic records, school reporting',
                        'meta_title' => 'Year-End Reporting With Less Panic | Astha Academics',
                        'meta_keywords' => 'school year end reports, academic records system, education reporting',
                        'meta_description' => 'Learn how organized school records make year-end reporting simpler and less stressful.',
                    ],
                    'bn' => [
                        'title' => 'কম চাপেই বছরশেষ রিপোর্টিং',
                        'description' => 'সারা বছর রেকর্ড গুছিয়ে রাখা গেলে বছরশেষ রিপোর্ট প্রস্তুত ও যাচাই অনেক সহজ হয়। এতে শিক্ষাবর্ষ শেষ করা যায় কম চাপ এবং বেশি আত্মবিশ্বাস নিয়ে।',
                        'seo_tags' => 'বছরশেষ রিপোর্টিং, একাডেমিক রেকর্ড, স্কুল রিপোর্ট',
                        'meta_title' => 'কম চাপেই বছরশেষ রিপোর্টিং | আস্থা একাডেমিক্স',
                        'meta_keywords' => 'স্কুল বছরশেষ রিপোর্ট, একাডেমিক রেকর্ড সিস্টেম, শিক্ষা রিপোর্টিং',
                        'meta_description' => 'গোছানো স্কুল রেকর্ড কীভাবে বছরশেষ রিপোর্ট সহজ করে তা জানুন।',
                    ],
                ],
            ],
        ];

        foreach ($blogs as $index => $payload) {
            $blog = Blog::withTrashed()->updateOrCreate(
                ['slug' => $payload['slug']],
                [
                    'image' => $payload['image'],
                    'status' => 'published',
                    'is_featured' => $payload['is_featured'],
                    'published_at' => now()->subDays(20 - $index)->setTime(10, 0),
                    'deleted_at' => null,
                ]
            );

            foreach ($payload['translations'] as $locale => $translation) {
                $blog->translations()->updateOrCreate(
                    ['locale' => $locale],
                    $translation
                );
            }
        }
    }
}
