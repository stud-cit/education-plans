<?php

namespace App\Console\Commands;

use App\Models\Plan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Helpers\GeneratePlanPdf as Generate;

class ConsoleGeneratePlanPdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:generatePdf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate pdfs for verified plans';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $plans = Plan::with('verification')->select('id', 'guid')->plan()->verified()->get();

        $this->withProgressBar($plans, function ($plan) {
            $path = 'plans/';
            $fileName = "{$plan->guid}.pdf";
            $publicPath = public_path("{$path}{$fileName}");

            if (file_exists($publicPath)) {
                Log::info("file exist $publicPath");
            } else {
                $pdf = new Generate;
                $pdf($plan->id);
                $pdf->consoleSave();
            }
        });

        return 0;
    }
}
