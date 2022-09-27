<?php

namespace App\Policies;

use App\Models\SubjectLanguage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubjectLanguagePolicy
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
     * @param  \App\Models\SubjectLanguage  $subjectLanguage
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, SubjectLanguage $subjectLanguage)
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
        return $user->possibility(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubjectLanguage  $subjectLanguage
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, SubjectLanguage $subjectLanguage)
    {
        return $user->possibility(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SubjectLanguage  $subjectLanguage
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, SubjectLanguage $subjectLanguage)
    {
        return $user->possibility(User::PRIVILEGED_ROLES);
    }
}
