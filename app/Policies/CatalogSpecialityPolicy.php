<?php

namespace App\Policies;

use App\Models\CatalogSpeciality;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CatalogSpecialityPolicy
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
     * @param  \App\Models\CatalogSpeciality  $catalogSpeciality
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, CatalogSpeciality $catalogSpeciality)
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
        return $user->possibility([User::ROOT, User::ADMIN, User::DEPARTMENT]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogSpeciality  $catalogSpeciality
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, CatalogSpeciality $catalogSpeciality)
    {
        // TODO check owner catalog
        // return $user->possibility(User::DEPARTMENT) && $catalogSpeciality->user_id ||
        // $user->possibility(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogSpeciality  $catalogSpeciality
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, CatalogSpeciality $catalogSpeciality)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogSpeciality  $catalogSpeciality
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, CatalogSpeciality $catalogSpeciality)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogSpeciality  $catalogSpeciality
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, CatalogSpeciality $catalogSpeciality)
    {
        //
    }
}
