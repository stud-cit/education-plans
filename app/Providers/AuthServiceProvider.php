<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use App\Models\CatalogSelectiveSubject;
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

        Gate::define('copy-plan', function (User $user) {
            return in_array($user->role_id, User::ALL_ROLES);
        });

        Gate::define('restore-catalog-group', function (User $user) {
            return $user->possibility(User::PRIVILEGED_ROLES);
        });

        Gate::define(
            'toggle-need-verification',
            function (User $user, CatalogSelectiveSubject $catalogSelectiveSubject) {

                return $user->id === $catalogSelectiveSubject->user_id
                    && $catalogSelectiveSubject->need_verification === false
                    || $user->possibility(User::PRIVILEGED_ROLES);
            }
        );
    }
}
