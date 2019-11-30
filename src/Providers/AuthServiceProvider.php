<?php

namespace Pktharindu\NovaPermissions\Providers;

use Illuminate\Support\Facades\Gate;
use Pktharindu\NovaPermissions\Traits\ValidatesPermissions;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    use ValidatesPermissions;
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->defineGates();
    }

    private function defineGates()
    {
        foreach (config('nova-permissions.permissions') as $key => $permissions) {
            Gate::define($key, function (User $user) use ($key) {
                if ($this->nobodyHasAccess($key)) {
                    return true;
                }

                return $user->hasPermissionTo($key);
            });
        }
    }
}
