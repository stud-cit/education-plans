<?php

namespace App\Policies;

use App\Models\ListCycle;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ListCyclePolicy
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
        return in_array($user->role_id, User::ROLE_LIST);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ListCycle  $listCycle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ListCycle $listCycle)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return in_array($user->role_id, User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ListCycle  $listCycle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ListCycle $listCycle)
    {
        return in_array($user->role_id, User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ListCycle  $listCycle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ListCycle $listCycle)
    {
        return in_array($user->role_id, User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ListCycle  $listCycle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ListCycle $listCycle)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ListCycle  $listCycle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ListCycle $listCycle)
    {
        //
    }
}
