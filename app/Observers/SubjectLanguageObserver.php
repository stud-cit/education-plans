<?php

namespace App\Observers;

use App\Models\SubjectLanguage;
use App\Http\Controllers\UserActivityController;

class SubjectLanguageObserver
{
    /**
     * Handle the SubjectLanguage "created" event.
     *
     * @param  \App\Models\SubjectLanguage  $subjectLanguage
     * @return void
     */
    public function created(SubjectLanguage $subjectLanguage)
    {
        UserActivityController::addToLog(__('variables.created'), 'SubjectLanguage', "SubjectLanguage id:{$subjectLanguage->id}");
    }

    /**
     * Handle the SubjectLanguage "updated" event.
     *
     * @param  \App\Models\SubjectLanguage  $subjectLanguage
     * @return void
     */
    public function updated(SubjectLanguage $subjectLanguage)
    {
        UserActivityController::addToLog(__('variables.updated'), 'SubjectLanguage', "SubjectLanguage id:{$subjectLanguage->id}");
    }

    /**
     * Handle the SubjectLanguage "deleted" event.
     *
     * @param  \App\Models\SubjectLanguage  $subjectLanguage
     * @return void
     */
    public function deleted(SubjectLanguage $subjectLanguage)
    {
        UserActivityController::addToLog(__('variables.deleted'), 'SubjectLanguage', "SubjectLanguage id:{$subjectLanguage->id}");
    }
}
