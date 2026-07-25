<?php

namespace Database\Seeders;

use App\Models\AboutPerson;
use Illuminate\Database\Seeder;

class AboutPersonSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->people() as $index => $person) {
            AboutPerson::query()->updateOrCreate(
                ['slug' => $person['slug']],
                array_merge($person, [
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ])
            );
        }
    }

    private function people(): array
    {
        return [
            [
                'image' => null,
                'slug' => 'founder-and-director',
                'name_en' => 'Ashraful Islam',
                'name_bn' => 'আশরাফুল ইসলাম',
                'designation_en' => 'Founder and Director',
                'designation_bn' => 'প্রতিষ্ঠাতা ও পরিচালক',
                'summary_en' => 'Leads the product vision with a focus on practical digital tools that school teams can use every day.',
                'summary_bn' => 'স্কুল টিমের দৈনন্দিন ব্যবহারযোগ্য বাস্তব ডিজিটাল টুল তৈরির লক্ষ্যে পণ্যের ভিশন পরিচালনা করেন।',
                'details_en' => "Ashraful Islam works closely with school operators to understand the day-to-day reality of administration, reporting, and communication. His focus is on turning that operational complexity into simpler digital workflows that are easier to trust and sustain.\n\nHe continues to shape the platform around practical school needs instead of abstract software ideas.",
                'details_bn' => "আশরাফুল ইসলাম স্কুল পরিচালকদের সঙ্গে ঘনিষ্ঠভাবে কাজ করে প্রশাসন, রিপোর্টিং এবং যোগাযোগের বাস্তব দৈনন্দিন চাহিদা বোঝেন। তাঁর লক্ষ্য হলো সেই জটিল কাজগুলোকে সহজ ডিজিটাল প্রবাহে রূপ দেওয়া।\n\nতিনি প্ল্যাটফর্মটিকে বাস্তব স্কুলের প্রয়োজন অনুযায়ী গড়ে তুলতে কাজ করে যাচ্ছেন।",
            ],
            [
                'image' => null,
                'slug' => 'operations-and-success-lead',
                'name_en' => 'Nusrat Jahan',
                'name_bn' => 'নুসরাত জাহান',
                'designation_en' => 'Operations and Success Lead',
                'designation_bn' => 'অপারেশনস ও সাকসেস লিড',
                'summary_en' => 'Helps schools onboard smoothly and ensures teams can turn the platform into reliable daily practice.',
                'summary_bn' => 'স্কুলগুলোকে সহজভাবে অনবোর্ড হতে সহায়তা করেন এবং টিমগুলোকে নিয়মিত ব্যবহারে সক্ষম করে তোলেন।',
                'details_en' => "Nusrat Jahan coordinates implementation, feedback, and support workflows so institutions can move from initial setup to confident usage with less friction.\n\nHer work keeps the product grounded in adoption, clarity, and school-side trust.",
                'details_bn' => "নুসরাত জাহান বাস্তবায়ন, ফিডব্যাক এবং সাপোর্ট প্রবাহ সমন্বয় করেন যাতে প্রতিষ্ঠানগুলো কম জটিলতায় সেটআপ থেকে আত্মবিশ্বাসী ব্যবহারে যেতে পারে।\n\nতাঁর কাজ পণ্যটিকে ব্যবহারযোগ্যতা, স্বচ্ছতা এবং স্কুল-ভিত্তিক আস্থার সঙ্গে যুক্ত রাখে।",
            ],
            [
                'image' => null,
                'slug' => 'academic-product-advisor',
                'name_en' => 'Mahmud Hasan',
                'name_bn' => 'মাহমুদ হাসান',
                'designation_en' => 'Academic Product Advisor',
                'designation_bn' => 'একাডেমিক প্রোডাক্ট অ্যাডভাইজর',
                'summary_en' => 'Brings academic workflow insight to the product, especially around routines, attendance, and exam operations.',
                'summary_bn' => 'রুটিন, উপস্থিতি এবং পরীক্ষাকেন্দ্রিক একাডেমিক কাজের বাস্তব অভিজ্ঞতা পণ্যে যুক্ত করেন।',
                'details_en' => "Mahmud Hasan helps map academic processes into clearer product behavior so teachers and administrators can manage core school tasks with less manual confusion.\n\nHis perspective keeps classroom realities visible in product decisions.",
                'details_bn' => "মাহমুদ হাসান একাডেমিক প্রক্রিয়াগুলোকে আরও পরিষ্কার পণ্যের আচরণে রূপ দিতে সহায়তা করেন, যাতে শিক্ষক ও প্রশাসকরা মূল স্কুলকাজ কম বিভ্রান্তিতে পরিচালনা করতে পারেন।\n\nতাঁর অভিজ্ঞতা পণ্যের সিদ্ধান্তে শ্রেণিকক্ষের বাস্তবতা ধরে রাখে।",
            ],
        ];
    }
}
