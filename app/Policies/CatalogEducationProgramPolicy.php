<?php

namespace App\Policies;

use App\Models\CatalogEducationProgram;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CatalogEducationProgramPolicy
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
     * @param  \App\Models\CatalogEducationProgram  $catalogEducationProgram
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, CatalogEducationProgram $catalogEducationProgram)
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
        return $user->possibility([User::ROOT, User::ADMIN, User::DEPARTMENT, User::FACULTY_INSTITUTE]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogEducationProgram  $catalogEducationProgram
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, CatalogEducationProgram $catalogEducationProgram)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogEducationProgram  $catalogEducationProgram
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, CatalogEducationProgram $catalogEducationProgram)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogEducationProgram  $catalogEducationProgram
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, CatalogEducationProgram $catalogEducationProgram)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogEducationProgram  $catalogEducationProgram
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, CatalogEducationProgram $catalogEducationProgram)
    {
        //
    }
}
