<?php

namespace Polirium\Core\Base\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Polirium\Core\Base\Http\Models\User;
use Polirium\Core\Base\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        Gate::after(function ($user, $ability) {
            return $user->isSuperAdmin();
        });
    }
}
