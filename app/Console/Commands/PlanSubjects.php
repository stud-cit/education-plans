<?php

namespace App\Console\Commands;

use App\Models\HoursModules;
use Illuminate\Console\Command;

class PlanSubjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:subjects {planId}';

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
        $planId = $this->argument('planId');
        $subjectsId = HoursModules::select('id', 'course', 'semester', 'module', 'hour', 'subject_id as s_id')->with('subject.id')->whereHas('subject', function ($querySubject) use ($planId) {
            $querySubject->with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
                $queryCycle->where('plan_id', $planId);
            });
        })->pluck('s_id');


        $this->info(implode(', ', $subjectsId->unique()->values()->all()));
    }
}
