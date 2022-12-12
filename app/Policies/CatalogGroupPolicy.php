<?php

namespace App\Policies;

use App\Models\CatalogGroup;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CatalogGroupPolicy
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
     * @param  \App\Models\CatalogGroup $catalogGroup
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, CatalogGroup $catalogGroup)
    {
        return $user->possibility(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CatalogGroup $catalogGroup
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, CatalogGroup $catalogGroup)
    {
        return $user->possibility(User::PRIVILEGED_ROLES);
    }
}
