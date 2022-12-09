<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CatalogSpeciality;
use App\Models\SpecialitySubject;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpecialitySubjectPolicy
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
     * @param  \App\Models\SpecialitySubject  $specialitySubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, SpecialitySubject $specialitySubject)
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
        // Skip action, because check in controller custom rule create-speciality-subject
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SpecialitySubject  $specialitySubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, SpecialitySubject $specialitySubject)
    {

        $catalog = CatalogSpeciality::with('owners')
            ->where('id', $specialitySubject->catalog_subject_id)
            ->first();

        $ids = array_column($catalog->owners->toArray(), 'department_id');

        return $user->possibility(User::DEPARTMENT)
            && $specialitySubject->user_id === $user->id
            || in_array($user->department_id, $ids) && $user->possibility(User::DEPARTMENT)
            || $user->possibility(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SpecialitySubject  $specialitySubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, SpecialitySubject $specialitySubject)
    {
        $catalog = CatalogSpeciality::with('owners')
            ->where('id', $specialitySubject->catalog_subject_id)
            ->first();

        $ids = array_column($catalog->owners->toArray(), 'department_id');

        return $user->possibility(User::DEPARTMENT) && $specialitySubject->user_id === $user->id
            || in_array($user->department_id, $ids) && $user->possibility(User::DEPARTMENT)
            || $user->possibility(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SpecialitySubject  $specialitySubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, SpecialitySubject $specialitySubject)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SpecialitySubject  $specialitySubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, SpecialitySubject $specialitySubject)
    {
        //
    }
}
