<?php

namespace App\Policies;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlanPolicy
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
        return $user->possibility();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Plan $plan)
    {
        return $user->possibility();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->possibility(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Plan $plan)
    {
        if ($user->possibility(User::PRIVILEGED_ROLES)) {
            return true;
        }

        if ($user->possibility(User::REPRESENTATIVE_DEPARTMENT_ROLES) && $plan->isNotTemplate()) {
            return true;
        }

        if ($user->possibility(User::FACULTY_INSTITUTE) && $plan->isNotTemplate() &&
            $user->isFacultyMine($plan->faculty_id) && $user->isPlanMine($plan->author_id)) {
            return true;
        }

        if ($user->possibility(User::DEPARTMENT) && $plan->isNotTemplate() &&
            $user->isDepartmentMine($plan->department_id) && $user->isPlanMine($plan->author_id)) {

            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Plan $plan)
    {
        if ($user->possibility(User::PRIVILEGED_ROLES)) {
            return true;
        }

        if ($user->possibility(User::FACULTY_INSTITUTE) && $plan->author_id === $user->id) {
            return true;
        }

        if ($user->possibility(User::REPRESENTATIVE_DEPARTMENT_ROLES) && $plan->author_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Plan $plan)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Plan $plan)
    {
        //
    }
}
