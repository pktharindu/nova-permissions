<?php

namespace Pktharindu\NovaPermissions\Policies;

use Illuminate\Support\Facades\Gate;

class Policy
{
    /**
     * Retrieves all registered policies from the Gate. Only policies registered in the application
     * can be assigned to groups.
     *
     * @return array
     */
    public static function all()
    {
        return array_keys(Gate::abilities());
    }
}
