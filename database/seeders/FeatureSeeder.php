<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->features() as $index => $feature) {
            Feature::query()->updateOrCreate(
                ['title_en' => $feature['title_en']],
                [
                    'icon' => $feature['icon'],
                    'title_bn' => $feature['title_bn'],
                    'description_en' => $feature['description_en'],
                    'description_bn' => $feature['description_bn'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }

    private function features(): array
    {
        return [
            [
                'icon' => 'layout-dashboard',
                'title_en' => 'Unified Dashboard',
                'title_bn' => 'একীভূত ড্যাশবোর্ড',
                'description_en' => 'Track attendance, fee updates, notices, and daily activity from one calm operational view.',
                'description_bn' => 'এক জায়গা থেকে উপস্থিতি, ফি আপডেট, নোটিশ এবং দৈনন্দিন কার্যক্রম পরিষ্কারভাবে দেখুন।',
            ],
            [
                'icon' => 'users',
                'title_en' => 'Student Directory',
                'title_bn' => 'শিক্ষার্থী ডিরেক্টরি',
                'description_en' => 'Store profiles, guardians, roll numbers, and academic records in a structured student directory.',
                'description_bn' => 'প্রোফাইল, অভিভাবক, রোল নম্বর এবং একাডেমিক তথ্য গুছিয়ে শিক্ষার্থী ডিরেক্টরিতে রাখুন।',
            ],
            [
                'icon' => 'graduation-cap',
                'title_en' => 'Teacher Profiles',
                'title_bn' => 'শিক্ষক প্রোফাইল',
                'description_en' => 'Manage teacher assignments, subjects, schedules, and contact details without scattered sheets.',
                'description_bn' => 'ছড়িয়ে থাকা শিট ছাড়াই শিক্ষক দায়িত্ব, বিষয়, সময়সূচি এবং যোগাযোগ তথ্য পরিচালনা করুন।',
            ],
            [
                'icon' => 'credit-card',
                'title_en' => 'Fee Collection',
                'title_bn' => 'ফি সংগ্রহ',
                'description_en' => 'Monitor payable fees, collections, dues, and receipts with clear finance visibility.',
                'description_bn' => 'প্রাপ্য ফি, সংগ্রহ, বকেয়া এবং রসিদ স্পষ্টভাবে পর্যবেক্ষণ করুন।',
            ],
            [
                'icon' => 'clipboard-list',
                'title_en' => 'Attendance Workflows',
                'title_bn' => 'উপস্থিতি ব্যবস্থাপনা',
                'description_en' => 'Record daily attendance quickly and keep class-level summaries ready for review.',
                'description_bn' => 'দৈনিক উপস্থিতি দ্রুত নিন এবং ক্লাসভিত্তিক সারসংক্ষেপ সবসময় প্রস্তুত রাখুন।',
            ],
            [
                'icon' => 'shield-check',
                'title_en' => 'Role-Based Access',
                'title_bn' => 'রোলভিত্তিক অনুমতি',
                'description_en' => 'Limit sensitive actions by role so each user sees only the tools they need.',
                'description_bn' => 'রোল অনুযায়ী অনুমতি নির্ধারণ করে প্রতিটি ব্যবহারকারীকে প্রয়োজনীয় টুলসই দেখান।',
            ],
            [
                'icon' => 'calendar-check',
                'title_en' => 'Class Routine',
                'title_bn' => 'ক্লাস রুটিন',
                'description_en' => 'Publish and manage routines so teachers and students always see the latest schedule.',
                'description_bn' => 'রুটিন প্রকাশ ও হালনাগাদ করুন যাতে শিক্ষক ও শিক্ষার্থীরা সবসময় সর্বশেষ সময়সূচি পায়।',
            ],
            [
                'icon' => 'messages-square',
                'title_en' => 'Notices & Messages',
                'title_bn' => 'নোটিশ ও বার্তা',
                'description_en' => 'Share notices, reminders, and updates from one organized communication panel.',
                'description_bn' => 'একটি গুছানো যোগাযোগ প্যানেল থেকে নোটিশ, স্মরণিকা এবং আপডেট পাঠান।',
            ],
            [
                'icon' => 'layout-dashboard',
                'title_en' => 'Exam Overview',
                'title_bn' => 'পরীক্ষা সারসংক্ষেপ',
                'description_en' => 'Keep exam schedules, progress, and publishing flow visible from one screen.',
                'description_bn' => 'এক স্ক্রিনে পরীক্ষা সূচি, অগ্রগতি এবং প্রকাশ প্রক্রিয়া দৃশ্যমান রাখুন।',
            ],
            [
                'icon' => 'clipboard-list',
                'title_en' => 'Result Publishing',
                'title_bn' => 'ফলাফল প্রকাশ',
                'description_en' => 'Prepare and publish results with a cleaner workflow for teachers and administrators.',
                'description_bn' => 'শিক্ষক ও প্রশাসনের জন্য পরিচ্ছন্ন প্রক্রিয়ায় ফলাফল প্রস্তুত ও প্রকাশ করুন।',
            ],
            [
                'icon' => 'users',
                'title_en' => 'Guardian Records',
                'title_bn' => 'অভিভাবক তথ্য',
                'description_en' => 'Maintain guardian contact information and student-family linkage without duplication.',
                'description_bn' => 'অতিরিক্ত পুনরাবৃত্তি ছাড়াই অভিভাবকের যোগাযোগ তথ্য ও শিক্ষার্থী-পরিবার সংযোগ সংরক্ষণ করুন।',
            ],
            [
                'icon' => 'messages-square',
                'title_en' => 'Parent Communication',
                'title_bn' => 'অভিভাবক যোগাযোগ',
                'description_en' => 'Send important updates to parents with a more reliable and traceable communication flow.',
                'description_bn' => 'অভিভাবকদের কাছে গুরুত্বপূর্ণ আপডেট নির্ভরযোগ্য ও অনুসরণযোগ্যভাবে পাঠান।',
            ],
            [
                'icon' => 'credit-card',
                'title_en' => 'Payment Tracking',
                'title_bn' => 'পেমেন্ট ট্র্যাকিং',
                'description_en' => 'Track partial payments, collection status, and payment history with less manual follow-up.',
                'description_bn' => 'কম ম্যানুয়াল অনুসরণে আংশিক পেমেন্ট, সংগ্রহ অবস্থা এবং পেমেন্ট হিস্টোরি ট্র্যাক করুন।',
            ],
            [
                'icon' => 'calendar-check',
                'title_en' => 'Event Calendar',
                'title_bn' => 'ইভেন্ট ক্যালেন্ডার',
                'description_en' => 'Coordinate exams, holidays, and school events through one shared academic calendar.',
                'description_bn' => 'একটি শেয়ারড একাডেমিক ক্যালেন্ডারে পরীক্ষা, ছুটি এবং স্কুল ইভেন্ট সমন্বয় করুন।',
            ],
            [
                'icon' => 'graduation-cap',
                'title_en' => 'Subject Assignment',
                'title_bn' => 'বিষয় বরাদ্দ',
                'description_en' => 'Assign subjects by class and teacher with a clearer structure for routine planning.',
                'description_bn' => 'রুটিন পরিকল্পনার সুবিধায় ক্লাস ও শিক্ষকভিত্তিক বিষয় বরাদ্দ পরিষ্কারভাবে সাজান।',
            ],
            [
                'icon' => 'shield-check',
                'title_en' => 'Secure Operations',
                'title_bn' => 'নিরাপদ পরিচালনা',
                'description_en' => 'Reduce operational mistakes with structured access, reliable flows, and safer record handling.',
                'description_bn' => 'গঠিত অনুমতি, নির্ভরযোগ্য প্রক্রিয়া এবং নিরাপদ রেকর্ড ব্যবস্থাপনায় ভুল কমান।',
            ],
            [
                'icon' => 'layout-dashboard',
                'title_en' => 'Admin Snapshot',
                'title_bn' => 'অ্যাডমিন স্ন্যাপশট',
                'description_en' => 'Give administrators a quick operating snapshot of key numbers and pending actions.',
                'description_bn' => 'প্রশাসকদের জন্য গুরুত্বপূর্ণ সংখ্যা ও বাকি কাজের দ্রুত সারসংক্ষেপ দেখান।',
            ],
            [
                'icon' => 'clipboard-list',
                'title_en' => 'Academic Records',
                'title_bn' => 'একাডেমিক রেকর্ড',
                'description_en' => 'Keep class performance, assessments, and academic notes organized in one system.',
                'description_bn' => 'এক সিস্টেমে ক্লাস পারফরম্যান্স, মূল্যায়ন এবং একাডেমিক নোট গুছিয়ে রাখুন।',
            ],
            [
                'icon' => 'users',
                'title_en' => 'Section Management',
                'title_bn' => 'সেকশন ব্যবস্থাপনা',
                'description_en' => 'Organize classes into sections with cleaner structure for students, teachers, and routines.',
                'description_bn' => 'শিক্ষার্থী, শিক্ষক ও রুটিনের সুবিধায় ক্লাসগুলোকে সেকশনে সুন্দরভাবে সাজান।',
            ],
            [
                'icon' => 'messages-square',
                'title_en' => 'Operational Updates',
                'title_bn' => 'অপারেশন আপডেট',
                'description_en' => 'Keep staff aligned by sharing internal updates, reminders, and school-level notices.',
                'description_bn' => 'স্টাফদের সমন্বিত রাখতে অভ্যন্তরীণ আপডেট, স্মরণিকা এবং স্কুল-স্তরের নোটিশ শেয়ার করুন।',
            ],
        ];
    }
}
