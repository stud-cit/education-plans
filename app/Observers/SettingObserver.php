<?php

namespace App\Observers;

use App\Models\Setting;
use App\Http\Controllers\UserActivityController;
use Illuminate\Support\Facades\Auth;

class SettingObserver
{
    /**
     * Handle the Setting "created" event.
     *
     * @param  \App\Models\Setting  $setting
     * @return void
     */
    public function created(Setting $setting)
    {
        if (Auth::check()) {
            UserActivityController::addToLog(
                __('variables.created'),
                'Редактор обмежень',
                "Створено ({$setting->title})"
            );
        }
    }

    /**
     * Handle the Setting "updated" event.
     *
     * @param  \App\Models\Setting  $setting
     * @return void
     */
    public function updated(Setting $setting)
    {
        if (Auth::check()) {
            UserActivityController::addToLog(
                __('variables.updated'),
                'Редактор обмежень',
                "Редаговано Редактор обмежень ({$setting->title})"
            );
        }
    }

    /**
     * Handle the Setting "deleted" event.
     *
     * @param  \App\Models\Setting  $setting
     * @return void
     */
    public function deleted(Setting $setting)
    {
        if (Auth::check()) {
            UserActivityController::addToLog(
                __('variables.deleted'),
                'Редактор обмежень',
                "Редактор обмежень ({$setting->id})"
            );
        }
    }
}
