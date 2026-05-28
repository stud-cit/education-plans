<?php

namespace App\Policies;

use App\Models\Signature;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SignaturePolicy
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
     * @param  \App\Models\Signature  $signature
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Signature $signature)
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
        return User::isNotRole($user->role_id, User::GUEST);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Signature  $signature
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Signature $signature)
    {
        return User::isNotRole($user->role_id, User::GUEST);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Signature  $signature
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Signature $signature)
    {
        return User::isNotRole($user->role_id, User::GUEST);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Signature  $signature
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Signature $signature)
    {
        return User::isNotRole($user->role_id, User::GUEST);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Signature  $signature
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Signature $signature)
    {
        return false;
    }
}
