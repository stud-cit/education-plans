<?php

namespace App\Observers;

use App\Models\Note;
use App\Http\Controllers\UserActivityController;

class NoteObserver
{
    /**
     * Handle the Note "created" event.
     *
     * @param  \App\Models\Note  $note
     * @return void
     */
    public function created(Note $note)
    {
        UserActivityController::addToLog(
            __('variables.created'),
            'Примітки',
            "({$note->abbreviation})"
        );
    }

    /**
     * Handle the Note "updated" event.
     *
     * @param  \App\Models\Note  $note
     * @return void
     */
    public function updated(Note $note)
    {
        UserActivityController::addToLog(
            __('variables.updated'),
            'Примітки',
            "id ({$note->id})"
        );
    }

    /**
     * Handle the Note "deleted" event.
     *
     * @param  \App\Models\Note  $note
     * @return void
     */
    public function deleted(Note $note)
    {
        UserActivityController::addToLog(
            __('variables.deleted'),
            'Примітки',
            "id ({$note->id})"
        );
    }
}
