<?php

namespace App\Policies;

use App\Models\FormStudy;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormStudyPolicy
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
     * @param  \App\Models\FormStudy  $formStudy
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, FormStudy $formStudy)
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
        return $user->possibility(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FormStudy  $formStudy
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, FormStudy $formStudy)
    {
        return $user->possibility(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FormStudy  $formStudy
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, FormStudy $formStudy)
    {
        return $user->possibility(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FormStudy  $formStudy
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, FormStudy $formStudy)
    {
        return $user->possibility(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FormStudy  $formStudy
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, FormStudy $formStudy)
    {
        return false;
    }
}
