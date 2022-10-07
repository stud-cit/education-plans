<?php

namespace App\Observers;

use App\Models\CatalogGroup;
use App\Http\Controllers\UserActivityController;

class CatalogGroupObserver
{
    /**
     * Handle the CatalogGroup "created" event.
     *
     * @param  \App\Models\CatalogGroup  $catalogGroup
     * @return void
     */
    public function created(CatalogGroup $catalogGroup)
    {
        UserActivityController::addToLog(
            __('variables.created'),
            'CatalogGroup',
            "CatalogGroup id:{$catalogGroup->id}"
        );
    }

    /**
     * Handle the CatalogGroup "updated" event.
     *
     * @param  \App\Models\CatalogGroup  $catalogGroup
     * @return void
     */
    public function updated(CatalogGroup $catalogGroup)
    {
        UserActivityController::addToLog(
            __('variables.updated'),
            'CatalogGroup',
            "CatalogGroup id:{$catalogGroup->id}"
        );
    }

    /**
     * Handle the CatalogGroup "deleted" event.
     *
     * @param  \App\Models\CatalogGroup  $catalogGroup
     * @return void
     */
    public function deleted(CatalogGroup $catalogGroup)
    {
        UserActivityController::addToLog(
            __('variables.deleted'),
            'CatalogGroup',
            "CatalogGroup id:{$catalogGroup->id}"
        );
    }

    /**
     * Handle the CatalogGroup "restored" event.
     *
     * @param  \App\Models\CatalogGroup  $catalogGroup
     * @return void
     */
    public function restored(CatalogGroup $catalogGroup)
    {
        UserActivityController::addToLog(
            __('variables.restored'),
            'CatalogGroup',
            "CatalogGroup id:{$catalogGroup->id}"
        );
    }
}
