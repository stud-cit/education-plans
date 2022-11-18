<?php

namespace App\Policies;

use App\Models\CatalogSelectiveSubject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CatalogSelectiveSubjectPolicy
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
     * @param  \App\Models\CatalogSelectiveSubject  $catalogSelectiveSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, CatalogSelectiveSubject $catalogSelectiveSubject)
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
        return $user->possibility([User::ADMIN, User::ROOT, User::DEPARTMENT]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogSelectiveSubject  $catalogSelectiveSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, CatalogSelectiveSubject $catalogSelectiveSubject)
    {
        return $user->id === $catalogSelectiveSubject->user_id
            && $catalogSelectiveSubject->need_verification === false
            || $user->possibility(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogSelectiveSubject  $catalogSelectiveSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, CatalogSelectiveSubject $catalogSelectiveSubject)
    {
        return $user->id === $catalogSelectiveSubject->user_id
            && $catalogSelectiveSubject->need_verification === false
            || $user->possibility(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogSelectiveSubject  $catalogSelectiveSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, CatalogSelectiveSubject $catalogSelectiveSubject)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogSelectiveSubject  $catalogSelectiveSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, CatalogSelectiveSubject $catalogSelectiveSubject)
    {
        //
    }
}
