<?php

namespace App\Console\Commands;

use App\ExternalServices\Asu\Profession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CatalogRestoreSpeciality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:catalog-speciality';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore speciality in catalog education program';

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
        $counterUpdates = 0;
        $professions = new Profession();
        $catalogs = DB::table('catalog_subjects')
            ->select('*')
            ->where('selective_discipline_id', 3)
            ->where('education_program_id', '!=', null)
            ->where('speciality_id', '=', null)
            ->get();

        foreach ($catalogs as $catalog) {
            $educationProgramId = $catalog->education_program_id;

            $numberAffectedRows = DB::table('catalog_subjects')->select('*')->where('id', $catalog->id)
                ->update([
                    'speciality_id' => $professions->getSpecialityByEducationProgram($educationProgramId)
                ]);

            $counterUpdates += $numberAffectedRows;
        }

        $this->info("{$counterUpdates} number affected Rows!");
        return 0;
    }
}
