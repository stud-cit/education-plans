<?php

namespace App\Policies;

use App\Models\StudyTerm;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudyTermPolicy
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
     * @param  \App\Models\StudyTerm  $studyTerm
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, StudyTerm $studyTerm)
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
        return $user->privilege(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StudyTerm  $studyTerm
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, StudyTerm $studyTerm)
    {
        return $user->privilege(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StudyTerm  $studyTerm
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, StudyTerm $studyTerm)
    {
        return $user->privilege(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StudyTerm  $studyTerm
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, StudyTerm $studyTerm)
    {
        return $user->privilege(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StudyTerm  $studyTerm
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, StudyTerm $studyTerm)
    {
        return false;
    }
}
