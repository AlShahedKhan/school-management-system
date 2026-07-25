<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SchoolFeeTemplate;
use App\Services\SchoolStudentFeeGenerationService;
use Carbon\Carbon;

class GenerateMonthlyFeesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fees:generate-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly fees (e.g. Tuition, Food) for all active templates on the 1st of every month.';

    /**
     * Execute the console command.
     */
    public function handle(SchoolStudentFeeGenerationService $generator)
    {
        $this->info('Starting monthly fee generation...');
        $monthYear = Carbon::now()->format('Y-m');

        // Fetch active templates assigned to monthly billing
        $templates = SchoolFeeTemplate::where('is_active', true)
            ->whereHas('assign', function ($q) {
                $q->where('payment_type', 'monthly');
            })
            ->get();

        $count = 0;
        foreach ($templates as $template) {
            try {
                $generator->generateMonthlyFees($template, $monthYear);
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed generating for Template ID {$template->id}: " . $e->getMessage());
            }
        }

        $this->info("Completed monthly fee generation for {$count} templates.");
    }
}
