<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\SchoolAdmitCardSetting;
use Illuminate\Database\Seeder;

class SchoolAdmitCardSettingSeeder extends Seeder
{
    public function run(): void
    {
        School::query()->pluck('id')->each(
            fn (int $schoolId) => SchoolAdmitCardSetting::forSchool($schoolId)
        );
    }
}
