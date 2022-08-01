<?php

namespace App\Observers;

use App\Models\StudyTerm;
use App\Http\Controllers\UserActivityController;

class StudyTermObserver
{
    /**
     * Handle the StudyTerm "created" event.
     *
     * @param  \App\Models\StudyTerm  $studyTerm
     * @return void
     */
    public function created(StudyTerm $studyTerm)
    {
        UserActivityController::addToLog(
            __('variables.created'),
            'Термін навчання',
            "{$studyTerm->title} ({$studyTerm->id})"
        );
    }

    /**
     * Handle the StudyTerm "updated" event.
     *
     * @param  \App\Models\StudyTerm  $studyTerm
     * @return void
     */
    public function updated(StudyTerm $studyTerm)
    {
        UserActivityController::addToLog(
            __('variables.updated'),
            'Термін навчання',
            "{$studyTerm->title} ({$studyTerm->id})"
        );
    }

    /**
     * Handle the StudyTerm "deleted" event.
     *
     * @param  \App\Models\StudyTerm  $studyTerm
     * @return void
     */
    public function deleted(StudyTerm $studyTerm)
    {
        UserActivityController::addToLog(
            __('variables.deleted'),
            'Термін навчання',
            "{$studyTerm->title} ({$studyTerm->id})"
        );
    }
}
