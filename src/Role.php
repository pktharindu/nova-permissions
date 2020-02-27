<?php

namespace Pktharindu\NovaPermissions;

use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Pktharindu\NovaPermissions\Policies\Policy;

class Role extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'permissions',
    ];

    protected $appends = [
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('nova-permissions.table_names.roles', 'roles'));
    }

    public function users()
    {
        return $this->belongsToMany(config('nova-permissions.user_model', 'App\User'), config('nova-permissions.table_names.role_user', 'role_user'), 'role_id', 'user_id');
    }

    public function getPermissions()
    {
        return $this->hasMany(Permission::class);
    }

    /**
     * Replace all existing permissions with a new set of permissions.
     *
     * @param array $permissions
     */
    public function setPermissions(array $permissions)
    {
        if (! $this->id) {
            $this->save();
        }

        $this->revokeAll();

        collect($permissions)->map(function ($permission) {
            $this->grant($permission);
        });
    }

    /**
     * Check if a user has a given permission.
     *
     * @param string $permission
     *
     * @return bool
     */
    public function hasPermission($permission)
    {
        return $this->getPermissions->contains('permission_slug', $permission);
    }

    /**
     * Give Permission to a Role.
     *
     * @param string $permission
     *
     * @return bool
     */
    public function grant($permission)
    {
        if ($this->hasPermission($permission)) {
            return true;
        }

        if (! array_key_exists($permission, Gate::abilities())) {
            abort(403, 'Unknown permission');
        }

        return Permission::create([
            'role_id'         => $this->id,
            'permission_slug' => $permission,
        ]);

        return false;
    }

    /**
     * Revokes a Permission from a Role.
     *
     * @param string $permission
     *
     * @return bool
     */
    public function revoke($permission)
    {
        if (\is_string($permission)) {
            return Permission::findOrFail($permission)->delete();
        }

        return false;
    }

    /**
     * Remove all permissions from this Role.
     */
    public function revokeAll()
    {
        return $this->getPermissions()->delete();
    }

    /**
     * Get a list of permissions.
     *
     * @return array
     */
    public function getPermissionsAttribute()
    {
        return Permission::where('role_id', $this->id)->get()->pluck('permission_slug')->toArray();
    }

    /**
     * Replace all existing permissions with a new set of permissions.
     *
     * @param array $permissions
     */
    public function setPermissionsAttribute(array $permissions)
    {
        if (! $this->id) {
            $this->save();
        }

        $this->revokeAll();

        collect($permissions)->map(function ($permission) {
            if (! \in_array($permission, Policy::all(), true)) {
                return;
            }

            $this->grant($permission);
        });
    }
}
