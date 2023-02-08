<?php

namespace App\Policies;

use App\Models\CatalogEducationProgram;
use App\Models\EducationProgramSubject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EducationProgramSubjectPolicy
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
     * @param  \App\Models\EducationProgramSubject  $educationProgramSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, EducationProgramSubject $educationProgramSubject)
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
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EducationProgramSubject  $educationProgramSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, EducationProgramSubject $educationProgramSubject)
    {
        $catalog = CatalogEducationProgram::with('owners')
            ->where('id', $educationProgramSubject->catalog_subject_id)
            ->first();

        if ($catalog) {
            $ids = array_column($catalog->owners->toArray(), 'department_id');

            return $user->possibility([User::DEPARTMENT, User::FACULTY_INSTITUTE]) &&
                $user->isOwner($educationProgramSubject->user_id) ||
                in_array($user->department_id, $ids) &&
                $user->possibility(User::DEPARTMENT) ||
                $user->possibility(User::PRIVILEGED_ROLES);
        }

        return $user->possibility([User::DEPARTMENT, User::FACULTY_INSTITUTE]) &&
            $user->isOwner($educationProgramSubject->user_id) ||
            $user->possibility(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EducationProgramSubject  $educationProgramSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, EducationProgramSubject $educationProgramSubject)
    {
        $catalog = CatalogEducationProgram::with('owners')
            ->where('id', $educationProgramSubject->catalog_subject_id)
            ->first();

        if ($catalog) {
            $ids = array_column($catalog->owners->toArray(), 'department_id');

            return $user->possibility([User::DEPARTMENT, User::FACULTY_INSTITUTE]) &&
                $user->isOwner($educationProgramSubject->user_id) ||
                in_array($user->department_id, $ids) && $user->possibility(User::DEPARTMENT) ||
                $user->possibility(User::PRIVILEGED_ROLES);
        }

        return $user->possibility([User::DEPARTMENT, User::FACULTY_INSTITUTE]) &&
            $user->isOwner($educationProgramSubject->user_id) ||
            $user->possibility(User::PRIVILEGED_ROLES);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EducationProgramSubject  $educationProgramSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, EducationProgramSubject $educationProgramSubject)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EducationProgramSubject  $educationProgramSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, EducationProgramSubject $educationProgramSubject)
    {
        //
    }
}
