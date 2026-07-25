<?php

namespace Database\Seeders;

use App\Models\Donate;
use App\Models\DonateCollection;
use Illuminate\Database\Seeder;

class DonateCollectionSeeder extends Seeder
{
    public function run(): void
    {
        $donates = Donate::all();
        if ($donates->isEmpty()) {
            $this->command->warn('No donate records found. Please run DonateSeeder first.');
            return;
        }
        for ($i = 1; $i <= 50; $i++) {
            $donate = $donates->random();
            $receiveDate = fake()->dateTimeBetween('-2 years', 'now');
            DonateCollection::create([
                'school_id'     => $donate->school_id,
                'donate_id'     => $donate->id,
                'paid_amount'   => random_int(500, (int) $donate->amount),
                'receive_month' => (int) $receiveDate->format('m'),
                'receive_year'  => (int) $receiveDate->format('Y'),
                'receive_date'  => $receiveDate->format('Y-m-d'),
                'note'          => fake()->optional()->sentence(),
                'created_at'    => $receiveDate,
                'updated_at'    => now(),
            ]);
        }
    }
}