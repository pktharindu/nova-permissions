<?php

namespace Pktharindu\NovaPermissions\Traits;

use Pktharindu\NovaPermissions\Permission;

trait ValidatesPermissions
{
    /**
     * If nobody has this permission, grant access to everyone
     * This avoids you from being locked out of your application.
     *
     * @param string $permission
     *
     * @return boolean
     */
    protected function nobodyHasAccess($permission)
    {
        if (! $requestedPermission = Permission::find($permission)) {
            return true;
        }

        return ! $requestedPermission->hasUsers();
    }
}
