<?php

namespace App\Console\Commands;

use App\Models\Plan;
use Illuminate\Console\Command;
use App\Helpers\GenerateCatalogPdf;
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
                $catalogPdf = new GenerateCatalogPdf($plan->id);
                if (!file_exists("catalogs/speciality/$fileName")) {
                    Log::info("file generated catalogs/speciality/{$fileName}");
                    $catalogPdf->generateCatalogSpecialityPdf();
                }
                if (!file_exists("catalogs/educationProgram/$fileName")) {
                    Log::info("file generated catalogs/educationProgram/{$fileName}");
                    $catalogPdf->generateCatalogEducationPdf();
                }
            } else {
                $pdf = new Generate;
                $pdf($plan->id);
                $pdf->consoleSave();
            }
        });

        return 0;
    }
}
