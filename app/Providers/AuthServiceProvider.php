<?php

namespace App\Providers;

use App\Models\Note;
use App\Models\User;
use App\Models\FormControl;
use App\Policies\NotePolicy;
use App\Policies\FormControlPolicy;
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
        \App\Models\Note::class => \App\Policies\NotePolicy::class,
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
        Gate::define('copy_plan', function(User $user) {
            return in_array($user->role_id, User::ALL_ROLES);
        });
    }
}
