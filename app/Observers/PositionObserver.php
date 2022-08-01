<?php

namespace App\Observers;

use App\Models\Position;
use App\Http\Controllers\UserActivityController;

class PositionObserver
{
    /**
     * Handle the Position "created" event.
     *
     * @param  \App\Models\Position  $position
     * @return void
     */
    public function created(Position $position)
    {
        UserActivityController::addToLog(
            __('variables.created'),
            'Посади',
            "({$position->id})"
        );
    }

    /**
     * Handle the Position "updated" event.
     *
     * @param  \App\Models\Position  $position
     * @return void
     */
    public function updated(Position $position)
    {
        UserActivityController::addToLog(
            __('variables.updated'),
            'Посади',
            "({$position->id})"
        );
    }

    /**
     * Handle the Position "deleted" event.
     *
     * @param  \App\Models\Position  $position
     * @return void
     */
    public function deleted(Position $position)
    {
        UserActivityController::addToLog(
            __('variables.deleted'),
            'Посади',
            "({$position->id})"
        );
    }
}
