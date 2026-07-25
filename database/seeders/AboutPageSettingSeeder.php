<?php

namespace Database\Seeders;

use App\Models\AboutPageSetting;
use App\Support\AboutPageDefaults;
use Illuminate\Database\Seeder;

class AboutPageSettingSeeder extends Seeder
{
    public function run(): void
    {
        AboutPageSetting::query()->updateOrCreate(
            ['id' => 1],
            AboutPageDefaults::seedAttributes()
        );
    }
}
