<?php

namespace App\Observers;

use App\Models\ListCycle;
use App\Http\Controllers\UserActivityController;

class ListCycleObserver
{
    /**
     * Handle the ListCycle "created" event.
     *
     * @param  \App\Models\ListCycle  $listCycle
     * @return void
     */
    public function created(ListCycle $listCycle)
    {
        UserActivityController::addToLog(
            __('variables.created'),
            'Цикли',
            "({$listCycle->title})"
        );
    }

    /**
     * Handle the ListCycle "updated" event.
     *
     * @param  \App\Models\ListCycle  $listCycle
     * @return void
     */
    public function updated(ListCycle $listCycle)
    {
        UserActivityController::addToLog(
            __('variables.updated'),
            'Цикли',
            "id {$listCycle->id} ({$listCycle->title})"
        );
    }

    /**
     * Handle the ListCycle "deleted" event.
     *
     * @param  \App\Models\ListCycle  $listCycle
     * @return void
     */
    public function deleted(ListCycle $listCycle)
    {
        UserActivityController::addToLog(
            __('variables.deleted'),
            'Цикли',
            "id {$listCycle->id}"
        );
    }
}
