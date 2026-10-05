<?php

namespace App\Console\Commands;

use App\Helpers\GenerateCatalogPdf;
use App\Models\Plan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ConsoleGenerateCatalogsPdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:generateCatalogPdf {--id=} {--guid=} {--year_from=} {--force}';

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
        $id = $this->option('id');
        $guid = $this->option('guid');
        $yearFrom = $this->option('year_from');
        $force = $this->option('force');

        $plans = Plan::query()->select(['id', 'guid', 'title', 'year', 'created_at'])->verified()
            ->when($id, fn($q) => $q->where('id', $id))
            ->when($guid, fn($q) => $q->where('guid', $guid))
            ->when($yearFrom, fn($q) => $q->where('year', '>=', $yearFrom))
            ->orderBy('created_at')
            ->get();

        $this->info("Count {$plans->count()}");
        $this->newLine();
        $this->withProgressBar($plans, function ($plan) use ($force) {
            if ($force) {
                $pdf = new GenerateCatalogPdf($plan->id);
                $pdf->generateCatalogSpecialityPdf();
                $pdf->generateCatalogEducationPdf();
            } else {
                $fileName = "{$plan->guid}.pdf";
                $catalogPdf = new GenerateCatalogPdf($plan->id);
                if (!file_exists("catalogs/speciality/$fileName")) {
                    Log::info("file generated catalogs/speciality/{$fileName}");
                    $catalogPdf->generateCatalogSpecialityPdf();
                }
                if (!file_exists("catalogs/educationProgram/$fileName")) {
                    Log::info("file generated catalogs/educationProgram/{$fileName}");
                    $catalogPdf->generateCatalogEducationPdf();
                }
            }
        });

        return 0;
    }
}
