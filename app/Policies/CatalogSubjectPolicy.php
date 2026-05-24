<?php

namespace App\Policies;

use App\Models\CatalogSubject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CatalogSubjectPolicy
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
     * @param  \App\Models\CatalogSubject  $catalogSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, CatalogSubject $catalogSubject)
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
        return $user->except($user->role_id, User::GUEST);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogSubject  $catalogSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, CatalogSubject $catalogSubject)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogSubject  $catalogSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, CatalogSubject $catalogSubject)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogSubject  $catalogSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, CatalogSubject $catalogSubject)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogSubject  $catalogSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, CatalogSubject $catalogSubject)
    {
        //
    }
}
