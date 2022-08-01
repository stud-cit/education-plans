<?php

namespace App\Observers;

use App\Models\SelectiveDiscipline;
use App\Http\Controllers\UserActivityController;

class SelectiveDisciplineObserver
{
    /**
     * Handle the SelectiveDiscipline "created" event.
     *
     * @param  \App\Models\SelectiveDiscipline  $selectiveDiscipline
     * @return void
     */
    public function created(SelectiveDiscipline $selectiveDiscipline)
    {
        UserActivityController::addToLog(
            __('variables.created'),
            'Вибіркові дисципліни',
            "{$selectiveDiscipline->title} ({$selectiveDiscipline->id})"
        );
    }

    /**
     * Handle the SelectiveDiscipline "updated" event.
     *
     * @param  \App\Models\SelectiveDiscipline  $selectiveDiscipline
     * @return void
     */
    public function updated(SelectiveDiscipline $selectiveDiscipline)
    {
        UserActivityController::addToLog(
            __('variables.updated'),
            'Вибіркові дисципліни',
            "{$selectiveDiscipline->title} ({$selectiveDiscipline->id})"
        );
    }

    /**
     * Handle the SelectiveDiscipline "deleted" event.
     *
     * @param  \App\Models\SelectiveDiscipline  $selectiveDiscipline
     * @return void
     */
    public function deleted(SelectiveDiscipline $selectiveDiscipline)
    {
        UserActivityController::addToLog(
            __('variables.deleted'),
            'Вибіркові дисципліни',
            "{$selectiveDiscipline->title} ({$selectiveDiscipline->id})"
        );
    }
}
