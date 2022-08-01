<?php

namespace App\Observers;

use App\Http\Controllers\UserActivityController;
use App\Models\Plan;
use Illuminate\Support\Facades\Request;

class PlanObserver
{
    /**
     * Handle the Plan "created" event.
     *
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    public function created(Plan $plan)
    {
        UserActivityController::addToLog(__('variables.created'), 'План', "План {$plan->id}");
    }

    /**
     * Handle the Plan "updated" event.
     *
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    public function updated(Plan $plan)
    {
        UserActivityController::addToLog(__('variables.updated'), 'План', "План {$plan->id}");
    }

    /**
     * Handle the Plan "deleted" event.
     *
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    public function deleted(Plan $plan)
    {
        UserActivityController::addToLog(__('variables.deleted'), 'План', "План {$plan->id}");
    }

    public function replicating(Plan $plan)
    {
        preg_match('/\d$/', Request::url(), $matches);

        UserActivityController::addToLog(__('variables.replicating'), 'План', "Початковий план {$matches[0]}");
    }
}
