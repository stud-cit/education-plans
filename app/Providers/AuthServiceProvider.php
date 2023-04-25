<?php

namespace App\Providers;

use App\Models\Plan;
use App\Models\User;
use App\Models\CatalogSpeciality;
use App\Models\SpecialitySubject;
use Illuminate\Support\Facades\Gate;
use App\Models\CatalogEducationProgram;
use App\Models\CatalogSelectiveSubject;
use App\Policies\SpecialitySubjectPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        '\App\Models\FormControl::class' => '\App\Policies\FormControlPolicy::class',
        SpecialitySubject::class => SpecialitySubjectPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('manage-study-terms', fn (User $user) => $user->possibility(User::PRIVILEGED_ROLES));

        Gate::define('copy-plan', function (User $user, Plan $plan) {
            return $user->possibility() && $plan->isNotShort();
        });

        Gate::define('restore-catalog-group', function (User $user) {
            return $user->possibility(User::PRIVILEGED_ROLES);
        });

        Gate::define(
            'toggle-need-verification',
            function (User $user, CatalogSelectiveSubject $catalogSelectiveSubject) {

                return $user->isOwner($catalogSelectiveSubject->user_id) &&
                    $catalogSelectiveSubject->need_verification === false ||
                    $user->possibility(User::PRIVILEGED_ROLES);
            }
        );

        Gate::define(
            'can-verification',
            function (User $user, CatalogSelectiveSubject $catalogSelectiveSubject) {
                $needVerification = $catalogSelectiveSubject->need_verification === true;

                return
                    $user->possibility(User::TRAINING_DEPARTMENT) && $needVerification ||
                    $user->possibility(User::EDUCATIONAL_DEPARTMENT_DEPUTY) && $needVerification ||
                    $user->possibility(User::EDUCATIONAL_DEPARTMENT_CHIEF) && $needVerification ||
                    $user->isFacultyMine($catalogSelectiveSubject->faculty_id) && $needVerification || // TODO: Можливо потрібно додати роль
                    $user->possibility(User::PRIVILEGED_ROLES);
            }
        );

        Gate::define('copy-catalog-speciality', function (User $user) {
            return $user->possibility([User::DEPARTMENT, User::FACULTY_INSTITUTE, User::ROOT, User::ADMIN]);
        });

        Gate::define('delete-catalog-speciality', function (User $user, CatalogSpeciality $catalogSpeciality) {
            if ($user->possibility(User::FACULTY_INSTITUTE)) {
                return $user->isFacultyMine($catalogSpeciality->faculty_id);
            }

            if ($user->possibility(User::DEPARTMENT)) {
                return $user->isDepartmentMine($catalogSpeciality->department_id);
            }

            return $user->possibility([User::ROOT, User::ADMIN]) || $user->isOwner($catalogSpeciality->user_id);
        });

        Gate::define('create-speciality-subject', function (User $user, $catalog_id) {
            $catalog = CatalogSpeciality::with('owners')->where('id', $catalog_id)->first();

            if ($catalog->isVerified()) {
                return false;
            }

            if (
                $user->isDepartmentMine($catalog->department_id)
                && $user->possibility([User::DEPARTMENT, User::FACULTY_INSTITUTE])
            ) {
                return true;
            }

            $ids = array_column($catalog->owners->toArray(), 'department_id');

            return $user->possibility([User::ROOT, User::ADMIN]) ||
                in_array($user->department_id, $ids) && $user->possibility([User::DEPARTMENT, User::FACULTY_INSTITUTE]) || $user->isOwner($catalog->user_id);
        });

        Gate::define(
            'can-verification-speciality-catalog',
            function (User $user, CatalogSpeciality $catalogSpeciality) {
                return
                    $user->possibility([User::ADMIN, User::ROOT]) ||
                    $user->isFacultyMine($catalogSpeciality->faculty_id) && $user->possibility(User::FACULTY_INSTITUTE);
            }
        );

        Gate::define(
            'toggle-need-verification-speciality-catalog',
            function (User $user, CatalogSpeciality $catalogSpeciality) {
                if ($user->isDepartmentMine($catalogSpeciality->department_id)) {
                    return true;
                }

                return
                    $user->possibility([User::ROOT, User::ADMIN]) ||
                    $user->isOwner($catalogSpeciality->user_id);
            }
        );

        Gate::define(
            'can-setting-catalog-speciality',
            function (User $user, CatalogSpeciality $catalogSpeciality) {
                if ($user->possibility(User::DEPARTMENT) && $user->isDepartmentMine($catalogSpeciality->department_id)) {
                    return true;
                }

                return
                    $user->possibility([User::ROOT, User::ADMIN]) ||
                    $user->isOwner($catalogSpeciality->user_id);
            }
        );
        //  EDUCATION PROGRAM
        Gate::define('copy-catalog-education-program', function (User $user) {
            return $user->possibility([User::DEPARTMENT, User::FACULTY_INSTITUTE, User::ROOT, User::ADMIN]);
        });

        Gate::define('delete-catalog-education-program', function (User $user, CatalogEducationProgram $catalogEducationProgram) {
            if ($user->possibility(User::FACULTY_INSTITUTE)) {
                return $user->isFacultyMine($catalogEducationProgram->faculty_id);
            }

            if ($user->possibility(User::DEPARTMENT)) {
                return $user->isDepartmentMine($catalogEducationProgram->department_id);
            }

            return $user->possibility([User::ROOT, User::ADMIN]) || $user->isOwner($catalogEducationProgram->user_id);
        });

        Gate::define('create-education-program-subject', function (User $user, $catalog_id) {
            $catalog = CatalogEducationProgram::with('owners')->where('id', $catalog_id)->first();

            if ($catalog->isVerified()) {
                return false;
            }

            if (
                $user->isDepartmentMine($catalog->department_id) &&
                $user->possibility([User::DEPARTMENT, User::FACULTY_INSTITUTE])
            ) {
                return true;
            }

            if ($user->isFacultyMine($catalog->faculty_id) && $user->possibility(User::FACULTY_INSTITUTE)) {
                return true;
            }

            $ids = array_column($catalog->owners->toArray(), 'department_id');

            return
                $user->possibility([User::ROOT, User::ADMIN]) ||
                in_array($user->department_id, $ids) && $user->possibility([User::DEPARTMENT, User::FACULTY_INSTITUTE]) ||
                $user->isOwner($catalog->user_id);
        });

        Gate::define(
            'can-verification-education-program-catalog',
            function (User $user, CatalogEducationProgram $catalogEducationProgram) {
                $facultyId = $catalogEducationProgram->faculty_id;

                return $user->possibility([User::ADMIN, User::ROOT]) ||
                    $user->isFacultyMine($facultyId) && $user->possibility(User::FACULTY_INSTITUTE);
            }
        );

        Gate::define(
            'toggle-need-verification-education-program-catalog',
            function (User $user, CatalogEducationProgram $catalogEducationProgram) {
                $departmentId = $catalogEducationProgram->department_id;

                if ($user->isDepartmentMine($departmentId)) {
                    return true;
                }

                return $user->possibility([User::ROOT, User::ADMIN]) ||
                    $user->isOwner($catalogEducationProgram->user_id);
            }
        );

        Gate::define(
            'can-setting-catalog-education-program',
            function (User $user, CatalogEducationProgram $catalogEducationProgram) {
                if (
                    $user->isDepartmentMine($catalogEducationProgram->department_id) &&
                    $user->possibility(User::DEPARTMENT)
                ) {
                    return true;
                }

                return
                    $user->possibility([User::ROOT, User::ADMIN]) ||
                    $user->isOwner($catalogEducationProgram->user_id);
            }
        );

        Gate::define('upload-manual', function (User $user) {
            return $user->possibility(User::ROOT);
        });
    }
}
