<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\GeneratePlanPdf as Generate;

class GeneratePlanPdfById extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:generate-pdf-by-id {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->info('PDF generation started');

        $pdf = new Generate;
        $pdf($this->argument('id'));
        $pdf->consoleSave();

        $this->info('PDF generated successfully');

        return 0;
    }
}
