# Laravel Nova Grouped Permissions (ACL)

![GitHub](https://img.shields.io/github/license/pktharindu/nova-permissions.svg?style=for-the-badge) ![Packagist](https://img.shields.io/packagist/dt/pktharindu/nova-permissions.svg?style=for-the-badge) ![Packagist](https://img.shields.io/packagist/v/pktharindu/nova-permissions.svg?style=for-the-badge)

Add Permissions based authorization for your Nova installation via User-based Roles and Permissions. Roles are defined in the database whereas Permissions are defined in the code base. It allows you to group your Permissions into Groups and attach it to Users.

This package is inspired by [Silvanite\Brandenburg](https://github.com/Silvanite/brandenburg) as it has clear separation of concerns.

> _Roles_ are defined in the _Database_

and

> _Permissions_ are defined in the _Codebase_

As a result, you won't see any _Permissions_ resource. The _Roles_ resource will get the permissions from the Gates defined in your code.

- [Laravel Nova Grouped Permissions (ACL)](#laravel-nova-grouped-permissions-acl)
  - [Installation](#installation)
  - [Permissions with Groups](#permissions-with-groups)
    - [Index View](#index-view)
    - [Detail View](#detail-view)
    - [Edit View](#edit-view)
  - [Usage](#usage)
    - [Create a Model Policy](#create-a-model-policy)
  - [Customization](#customization)
    - [Use your own Resources](#use-your-own-resources)
  - [Credits](#credits)

![Tool Demo](https://raw.githubusercontent.com/pktharindu/nova-permissions/master/docs/preview-demo.gif)

## Installation

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require pktharindu/nova-permissions
```

Publish the Migration with the following command:

```bash
php artisan vendor:publish --provider="Pktharindu\NovaPermissions\ToolServiceProvider" --tag="migrations"
```

Migrate the Database:

```bash
php artisan migrate
```

Publish the Configuration with the following command:

```bash
php artisan vendor:publish --provider="Pktharindu\NovaPermissions\ToolServiceProvider" --tag="config"
```

Configuration file includes some dummy permissions for your refference. Feel free to remove them and add your own permissions.

```php
// in config/novapermissions.php

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User model class
    |--------------------------------------------------------------------------
    */

    'userModel' => 'App\User',

    /*
    |--------------------------------------------------------------------------
    | Nova User resource tool class
    |--------------------------------------------------------------------------
    */

    'userResource' => 'App\Nova\User',

    /*
    |--------------------------------------------------------------------------
    | The group associated with the resource
    |--------------------------------------------------------------------------
    */

    'roleResourceGroup' => 'Other',

    /*
    |--------------------------------------------------------------------------
    | Application Permissions
    |--------------------------------------------------------------------------
    */

    'permissions' => [
        'view users' => [
            'display_name' => 'View users',
            'description'  => 'Can view users',
            'group'        => 'User',
        ],

        'create users' => [
            'display_name' => 'Create users',
            'description'  => 'Can create users',
            'group'        => 'User',
        ],

        // ...
    ],
];

```

Next up, you must register the tool with Nova. This is typically done in the `tools` method of the `NovaServiceProvider`.

```php
// in app/Providers/NovaServiceProvider.php

public function tools()
{
    return [
        // ...
        new \Pktharindu\NovaPermissions\NovaPermissions(),
    ];
}
```

After that, define the gates in the `boot` method of the `AuthServiceProvider` like below.

```php
// in app/Providers/AuthServiceProvider.php

use Illuminate\Support\Facades\Gate;
use Pktharindu\NovaPermissions\Traits\ValidatesPermissions;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    use ValidatesPermissions;

    // ...

    public function boot()
    {
        $this->registerPolicies();

        foreach (config('novapermissions.permissions') as $key => $permissions) {
            Gate::define($key, function (User $user) use ($key) {
                if ($this->nobodyHasAccess($key)) {
                    return true;
                }

                return $user->hasPermissionTo($key);
            });
        }
    }
}
```

Then, use `HasRoles` Traits in your `User` model.

```php
// in app/User.php

use Illuminate\Notifications\Notifiable;
use Pktharindu\NovaPermissions\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasRoles,
        Notifiable;

    // ...
}
```

Finally, add `BelongsToMany` fields to you `app/Nova/User` resource:

```php
use Laravel\Nova\Fields\BelongsToMany;

public function fields(Request $request)
{
    return [
        // ...
        BelongsToMany::make('Roles', 'roles', \Pktharindu\NovaPermissions\Nova\Role::class),
    ];
}
```

A new resource called **Roles** will appear in your Nova app after installing this package.

## Permissions with Groups

### Index View

![Detail View](https://raw.githubusercontent.com/pktharindu/nova-permissions/master/docs/index-view.png)

### Detail View

![Detail View](https://raw.githubusercontent.com/pktharindu/nova-permissions/master/docs/detail-view.png)

### Edit View

![Edit View](https://raw.githubusercontent.com/pktharindu/nova-permissions/master/docs/edit-view.png)

## Usage

### Create a Model Policy

You can create Model Policy that works with Nova and check permissions.

For Example: Create a new Post Policy with `php artisan make:policy PostPolicy` with the following code:

```php
<?php

namespace App\Policies;

use App\Post;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the post.
     *
     * @param \App\User $user
     * @param \App\Post $post
     *
     * @return mixed
     */
    public function view(User $user, Post $post)
    {
        if ($user->hasPermissionTo('view own posts')) {
            return $user->id === $post->user_id;
        }

        return $user->hasPermissionTo('view posts');
    }

    /**
     * Determine whether the user can create posts.
     *
     * @param \App\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAnyPermission(['manage posts', 'manage own posts']);
    }

    /**
     * Determine whether the user can update the post.
     *
     * @param \App\User $user
     * @param \App\Post $post
     *
     * @return mixed
     */
    public function update(User $user, Post $post)
    {
        if ($user->hasPermissionTo('manage own posts')) {
            return $user->id == $post->user_id;
        }
        return $user->hasPermissionTo('manage posts');
    }

    /**
     * Determine whether the user can delete the post.
     *
     * @param \App\User $user
     * @param \App\Post $post
     *
     * @return mixed
     */
    public function delete(User $user, Post $post)
    {
        if ($user->hasPermissionTo('manage own posts')) {
            return $user->id === $post->user_id;
        }

        return $user->hasPermissionTo('manage posts');
    }
}
```

It should now work as exptected. Just create a Role, modify its Permissions and the Policy should take care of the rest.

> **Note**: Don't forget to add your Policy to your `$policies` in `App\Providers\AuthServiceProvider` and define the permissions in `config\novapermissions.php`.

> `hasPermissionTo()` method determine if any of the assigned roles to this user have a specific permission.

> `hasAnyPermission()` method determine if the model has any of the given permissions.

> `hasAllPermissions()` method determine if the model has all of the given permissions.

> `view own posts` is superior to `view posts` and allows the User to only view his own posts.

> `manage own posts` is superior to `manage posts` and allows the User to only manage his own posts.

## Customization

### Use your own Resources

If you want to use your own role resource, you can define it when you register the tool:

```php
// in app/Providers/NovaServiceProvider.php

// ...

use App\Nova\Role;

public function tools()
{
    return [
        // ...
        \Pktharindu\NovaPermissions\NovaPermissions::make()
            ->roleResource(Role::class),
    ];
}
```

Then extend the `Pktharindu\NovaPermissions\Nova\Role` in your role resource:

```php
// in app/Nova/Role.php

use Pktharindu\NovaPermissions\Nova\Role as RoleResource;

class Role extends RoleResource
{
    // ...  
}
```

## Credits

This Package is inspired by [eminiarts/nova-permissions](https://novapackages.com/packages/eminiarts/nova-permissions) and [silvanite/novatoolpermissions](https://novapackages.com/packages/silvanite/novatoolpermissions). I wanted to have a combination of both. Thanks to both authors.
