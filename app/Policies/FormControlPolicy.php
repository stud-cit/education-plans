<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FormControl;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormControlPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        clock('FormControlPolicy viewAny');
        //return $user->role_id === 2;
        return in_array($user->role_id, User::ROLE_LIST);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FormControl  $formControl
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, FormControl $formControl)
    {
        clock('FormControlPolicy view');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        clock('FormControlPolicy create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FormControl  $formControl
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, FormControl $formControl)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FormControl  $formControl
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, FormControl $formControl)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FormControl  $formControl
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, FormControl $formControl)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FormControl  $formControl
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, FormControl $formControl)
    {
        //
    }
}
