<?php

namespace App\Console\Commands;

use App\Models\HoursModules;
use Illuminate\Console\Command;

class FixHoursModules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:fixHoursModules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'FixHoursModules';

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
        foreach ($plans as $key => $plan) {
            $amount_module = $plan->studyTerm->module;
            $form_organization_id = $plan->form_organization_id;
            $amount_module = $form_organization_id === MODULE_CYCLING ? $amount_module * 2 : $amount_module;

            $this->fix1($plan->id, $amount_module, $plan->study_term_id, $form_organization_id);
        }
        return 0;
    }

    function fix1($planId, $amount_module, $study_term_id, $form_organization_id)
    {
        $semestersWithHours = HoursModules::select('id', 'course', 'semester', 'module', 'hour', 'subject_id as s_id')->with('subject.id')->whereHas('subject', function ($querySubject) use ($planId) {
            $querySubject->with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
                $queryCycle->where('plan_id', $planId);
            });
        })->get();

        $module = 0;
        foreach ($semestersWithHours as $item) {
            $module++;

            if ($form_organization_id === 1) { // form_organization_id 1 Модульно циклова
                if ($study_term_id === 3 || $study_term_id === 6) {
                    $item->update(['module' => $module]);
                }

                if ($study_term_id === 1) { // Термін навчання 3 роки 10 місяців
                    $item->update(['module' => $module, 'course' => (int)round($item->semester / 2)]);
                }
            }

            if ($form_organization_id === 3) { // form_organization_id 3 модульно семестрова

                if ($study_term_id === 3 || $study_term_id === 1) {
                    $item->update(['module' => $module]);
                }

                if ($study_term_id === 6 || $study_term_id === 7 || $study_term_id === 8) {
                    $item->update(['module' => $module, 'course' => (int)round($item->semester / 2)]);
                }
            }

            if ($module % $amount_module === 0) {
                $module = 0;
            }
        }
    }
}
