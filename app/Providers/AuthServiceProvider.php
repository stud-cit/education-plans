<?php

namespace App\Providers;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
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

        Gate::define('manage_study_terms', fn(User $user) => $user->possibility(User::PRIVILEGED_ROLES));

        // TODO: NEED TO CHECK DEPARTMENT FACULTY
        Gate::define('copy-plan', function(User $user) {
            return in_array($user->role_id, User::ALL_ROLES);
        });
    }
}
