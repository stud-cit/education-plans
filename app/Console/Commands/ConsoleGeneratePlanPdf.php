<?php

namespace App\Console\Commands;

use App\Models\Plan;
use Illuminate\Console\Command;
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
        // ->where('form_organization_id', 1)
        $plans = Plan::with('verification')->select('id')->plan()->verified()->get();

        $this->withProgressBar($plans, function ($plan) {
            $pdf = new Generate;
            $pdf($plan->id);
            $pdf->consoleSave();
        });

        return 0;
    }
}
