<?php

namespace App\Policies;

use App\Models\Cycle;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CyclePolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Cycle  $cycle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Cycle $cycle)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if ($user->possibility()) {
            return true;
        }

        if ($user->possibility(User::ADMIN_DEPARTMENT_POSTGRADUATE)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Cycle  $cycle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Cycle $cycle)
    {
        if ($user->possibility()) {
            return true;
        }

        if ($user->possibility(User::FACULTY_INSTITUTE) && $cycle->plan->isFacultyMine()) {
            return true;
        }

        if ($user->possibility(User::DEPARTMENT) && $cycle->plan->isDepartmentMine()) {
            return true;
        }

        if (
            $user->possibility(User::ADMIN_DEPARTMENT_POSTGRADUATE) &&
            ($cycle->plan->isMine() || $cycle->plan->isPostgraduate())
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Cycle  $cycle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Cycle $cycle)
    {
        if ($user->possibility()) {
            return true;
        }

        if (
            $user->possibility(User::ADMIN_DEPARTMENT_POSTGRADUATE) &&
            ($cycle->plan->isMine() || $cycle->plan->isPostgraduate())
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Cycle  $cycle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Cycle $cycle)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Cycle  $cycle
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Cycle $cycle)
    {
        //
    }
}
