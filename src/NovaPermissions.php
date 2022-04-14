<?php

namespace Pktharindu\NovaPermissions;

use Illuminate\Http\Request;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Pktharindu\NovaPermissions\Nova\Role;

class NovaPermissions extends Tool
{
    protected $roleResource = Role::class;

    private $customRole = false;

    /**
     * Perform any tasks that need to happen when the tool is booted.
     */
    public function boot()
    {
        Nova::script('NovaPermissions', __DIR__.'/../dist/js/tool.js');

        if (! $this->customRole) {
            Nova::resources([
                $this->roleResource,
            ]);
        }
    }

    /**
     * @param string $roleResource
     *
     * @return mixed
     */
    public function roleResource(string $roleResource)
    {
        $this->customRole = true;

        $this->roleResource = $roleResource;

        return $this;
    }

    public function menu(Request $request)
    {
        return null;
    }
}
