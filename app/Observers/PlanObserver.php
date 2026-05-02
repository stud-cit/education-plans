<?php

namespace App\Observers;

use App\Http\Controllers\UserActivityController;
use App\Models\Plan;
use Illuminate\Support\Facades\Request;

class PlanObserver
{
    /**
     * Handle the Plan "created" event.
     */
    public function created(Plan $plan): void
    {
        if (auth()->check()) {
            UserActivityController::addToLog(__('variables.created'), 'План', "План {$plan->id}");
        }
    }

    public function updated(Plan $plan): void
    {
        if (auth()->check()) {
            UserActivityController::addToLog(__('variables.updated'), 'План', "План {$plan->id}");
        }
    }

    /**
     * Handle the Plan "deleted" event.
     */
    public function deleted(Plan $plan): void
    {
        if (auth()->check()) {
            UserActivityController::addToLog(__('variables.deleted'), 'План', "План {$plan->id}");
        }
    }

    public function replicating(Plan $plan): void
    {
        if (auth()->check()) {
            preg_match('/\d$/', Request::url(), $matches);

            UserActivityController::addToLog(__('variables.replicating'), 'План', "Початковий план {$matches[0]}");
        }
    }

    /**
     * Handle the Plan "restored" event.
     */
    public function restored(Plan $plan): void
    {
        if (auth()->check()) {
            UserActivityController::addToLog(__('variables.restored'), 'План', "План {$plan->id}");
        }
    }
}
