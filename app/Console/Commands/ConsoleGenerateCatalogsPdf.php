<?php

namespace App\Console\Commands;

use App\Helpers\GenerateCatalogPdf;
use App\Models\Plan;
use Illuminate\Console\Command;

class ConsoleGenerateCatalogsPdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:generateCatalogPdf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate pdf catalogs to verified plans';

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
        $plans = Plan::with('verification')->select('id')->plan()->verified()->get();

        $this->withProgressBar($plans, function ($plan) {
            $pdf = new GenerateCatalogPdf($plan->id);
            $pdf->generateCatalogSpecialityPdf();
            $pdf->generateCatalogEducationPdf();
        });

        return 0;
    }
}
