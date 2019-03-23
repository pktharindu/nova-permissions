<?php

namespace Pktharindu\NovaPermissions;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Pktharindu\NovaPermissions\Nova\Role;

class NovaPermissions extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     */
    public function boot()
    {
        Nova::script('NovaPermissions', __DIR__.'/../dist/js/tool.js');
        Nova::style('NovaPermissions', __DIR__.'/../dist/css/tool.css');

        Nova::resources([
            Role::class,
        ]);
    }
}
