<?php

namespace Pktharindu\NovaPermissions\Traits;

use Pktharindu\NovaPermissions\Role;

trait HasRoles
{
    /**
     * Get all Roles given to this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->with('getPermissions');
    }

    /**
     * Scope a query to eager load `roles` relationship
     * to reduce database queries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRoles($query)
    {
        return $query->with('roles');
    }

    /**
     * Determine if any of the assigned roles to this user
     * have a specific permission.
     *
     * @param string $permission
     *
     * @return bool
     */
    public function hasPermissionTo($permission)
    {
        return $this->roles->contains(function ($role) use ($permission) {
            return $role->getPermissions->contains('permission_slug', $permission);
        });
    }

    /**
     * Determine if the model has any of the given permissions.
     *
     * @param array ...$permissions
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function hasAnyPermission(...$permissions): bool
    {
        if (\is_array($permissions[0])) {
            $permissions = $permissions[0];
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermissionTo($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the model has all of the given permissions.
     *
     * @param array ...$permissions
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function hasAllPermissions(...$permissions): bool
    {
        if (\is_array($permissions[0])) {
            $permissions = $permissions[0];
        }

        foreach ($permissions as $permission) {
            if (! $this->hasPermissionTo($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Assign a role to this user.
     *
     * @param string|Role $role
     *
     * @return bool
     */
    public function assignRole($role)
    {
        if (\is_string($role)) {
            return $this->roles()->attach(Role::where('slug', $role)->first());
        }

        return $this->roles()->attach($role);
    }

    /**
     * Remove a role from this user.
     *
     * @param string|Role $role
     *
     * @return bool
     */
    public function removeRole($role)
    {
        if (\is_string($role)) {
            return $this->roles()->detach(Role::where('slug', $role)->first());
        }

        return $this->roles()->detach($role);
    }

    /**
     * Reassign roles from an id or an array of role Ids.
     *
     * @param int|array $roles
     */
    public function setRolesById($roles)
    {
        $roles = \is_array($roles) ? $roles : [$roles];

        return $this->roles()->sync($roles);
    }
}
