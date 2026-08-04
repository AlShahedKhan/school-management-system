<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleUserSeeder::class,
            DynamicOperationSeeder::class,
            PackageSeeder::class,
            HomePageSettingsSeeder::class,
            AboutPageSettingSeeder::class,
            AboutPersonSeeder::class,
            FeatureSeeder::class,
            PageShowcaseSeeder::class,
            BlogSeeder::class,
            DemoRequestSeeder::class,
            DashboardNewsSeeder::class,
            PublicTranslationSeeder::class,
            SchoolExpenseSeeder::class,
            SchoolPayrollSeeder::class,
            SchoolPaymentSeeder::class,
            SchoolHolidaySeeder::class,
            DonateSeeder::class,
            DonateCollectionSeeder::class,
            EmployeeSeeder::class,
            EmployeePayrollSeeder::class,
            AdminSmsTemplateSeeder::class,
            SchoolExpenseSeeder::class,
            SchoolPayrollSeeder::class,
            SchoolPaymentSeeder::class,
        ]);
    }
}
