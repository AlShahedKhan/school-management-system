<?php

namespace Database\Seeders;

use App\Models\HomePageSetting;
use App\Support\HomePageDefaults;
use Illuminate\Database\Seeder;

class HomePageSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = HomePageSetting::firstOrNew(['id' => 1]);
        $defaults = HomePageDefaults::seedAttributes();

        foreach ($defaults as $field => $value) {
            if (blank($settings->{$field})) {
                $settings->{$field} = $value;
            }
        }

        $settings->save();
    }
}
