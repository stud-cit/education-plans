<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserActivityController;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        if (Auth::check()) {
            UserActivityController::addToLog(
                __('variables.created'),
                'Користувач',
                "Користувач {$user->fullName} ({$user->id})"
            );
        }
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        // TODO: !need refactor
        if (Auth::check()) {
            UserActivityController::addToLogV2('Редагування', 'Користувач', $user);
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        if (Auth::check()) {
            UserActivityController::addToLog('Видалення', 'Користувач', "Користувач {$user->fullName} ({$user->id})");
        }
    }
}
