<?php

namespace App\Console\Commands;

use App\Models\SemestersCredits;
use Illuminate\Console\Command;

class FixSemesterCredits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:fixSemesterCredits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix semester credits in subject belongs to plan';

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
        define("MODULE_CYCLING", 1); // Модульно циклова

        $plans = \App\Models\Plan::with('studyTerm')
            ->select('id', 'form_organization_id', 'study_term_id')
            ->get();

        // loop over plans
        foreach ($plans as $plan) {
            $this->update($plan->id, $plan->study_term_id, $plan->form_organization_id);
        }


        return 0;
    }
    function update($planId, $study_term_id, $form_organization_id)
    {
        $semestersCredits = SemestersCredits::select('id', 'course', 'semester', 'subject_id as s_id')
            ->with('subject.id')->whereHas('subject', function ($querySubject) use ($planId) {
                $querySubject->with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
                    $queryCycle->where('plan_id', $planId);
                });
            })->get();

        foreach ($semestersCredits as $item) {

            if ($form_organization_id === 1) { // form_organization_id 1 Модульно циклова
                if ($study_term_id === 1) { // Термін навчання 3 роки 10 місяців
                    $item->update(['course' => (int)round($item->semester / 2)]);
                }
            }

            if ($form_organization_id === 3) { // form_organization_id 3 модульно семестрова

                if ($study_term_id === 6 || $study_term_id === 7 || $study_term_id === 8) {
                    $item->update(['course' => (int)round($item->semester / 2)]);
                }
            }
        }
    }
}
