<?php

namespace App\Observers;

use App\Models\PlanVerification;

class VerificationObserver
{
    /**
     * Handle the PlanVerification "created" event.
     *
     * @param  \App\Models\PlanVerification  $planVerification
     * @return void
     */
    public function created(PlanVerification $planVerification)
    {
        if ($planVerification->status === true) {
            $plan = $planVerification->plan;
            $allVerification = $plan->isApprovedPlan();

            if ($allVerification) {
                $plan->touch();
            }
        }
    }

    /**
     * Handle the PlanVerification "updated" event.
     *
     * @param  \App\Models\PlanVerification  $planVerification
     * @return void
     */
    public function updated(PlanVerification $planVerification)
    {
        
        if ($planVerification->wasChanged('status') && $planVerification->status == true) {
            $plan = $planVerification->plan;
            $allVerification = $plan->approvedPlan;
            
            if ($allVerification) {
                $plan->touch();
            }
        }
    }

    /**
     * Handle the PlanVerification "deleted" event.
     *
     * @param  \App\Models\PlanVerification  $planVerification
     * @return void
     */
    public function deleted(PlanVerification $planVerification)
    {
        //
    }

    /**
     * Handle the PlanVerification "restored" event.
     *
     * @param  \App\Models\PlanVerification  $planVerification
     * @return void
     */
    public function restored(PlanVerification $planVerification)
    {
        //
    }

    /**
     * Handle the PlanVerification "force deleted" event.
     *
     * @param  \App\Models\PlanVerification  $planVerification
     * @return void
     */
    public function forceDeleted(PlanVerification $planVerification)
    {
        //
    }
}
