<?php

namespace Database\Seeders;

use App\Models\PageShowcase;
use Illuminate\Database\Seeder;

class PageShowcaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->showcases() as $showcase) {
            PageShowcase::query()->updateOrCreate(
                ['title_en' => $showcase['title_en']],
                [
                    'title' => $showcase['title_en'],
                    'title_bn' => $showcase['title_bn'],
                    'image' => $showcase['image'],
                ]
            );
        }
    }

    private function showcases(): array
    {
        return [
            [
                'title_en' => 'Smart Classrooms Overview',
                'title_bn' => 'স্মার্ট ক্লাসরুমের সারসংক্ষেপ',
                'image' => 'showcase_images/seeded/showcase-1.jpg',
            ],
            [
                'title_en' => 'Student Performance Dashboard',
                'title_bn' => 'শিক্ষার্থী পারফরম্যান্স ড্যাশবোর্ড',
                'image' => 'showcase_images/seeded/showcase-2.jpg',
            ],
            [
                'title_en' => 'Digital Attendance Workflow',
                'title_bn' => 'ডিজিটাল উপস্থিতি ওয়ার্কফ্লো',
                'image' => 'showcase_images/seeded/showcase-3.jpg',
            ],
            [
                'title_en' => 'Teacher Collaboration Space',
                'title_bn' => 'শিক্ষক সহযোগিতা স্পেস',
                'image' => 'showcase_images/seeded/showcase-4.jpg',
            ],
            [
                'title_en' => 'Modern School Reception',
                'title_bn' => 'আধুনিক স্কুল রিসেপশন',
                'image' => 'showcase_images/seeded/showcase-5.jpg',
            ],
            [
                'title_en' => 'Library and Learning Hub',
                'title_bn' => 'লাইব্রেরি ও লার্নিং হাব',
                'image' => 'showcase_images/seeded/showcase-6.jpg',
            ],
            [
                'title_en' => 'School Event Coordination',
                'title_bn' => 'স্কুল ইভেন্ট সমন্বয়',
                'image' => 'showcase_images/seeded/showcase-7.jpg',
            ],
            [
                'title_en' => 'Parent Communication Moment',
                'title_bn' => 'অভিভাবক যোগাযোগ মুহূর্ত',
                'image' => 'showcase_images/seeded/showcase-8.jpg',
            ],
            [
                'title_en' => 'Campus Operations Snapshot',
                'title_bn' => 'ক্যাম্পাস অপারেশন স্ন্যাপশট',
                'image' => 'showcase_images/seeded/showcase-9.jpg',
            ],
            [
                'title_en' => 'Confident Digital Learning',
                'title_bn' => 'আত্মবিশ্বাসী ডিজিটাল লার্নিং',
                'image' => 'showcase_images/seeded/showcase-10.jpg',
            ],
        ];
    }
}
