<?php

namespace Pktharindu\NovaPermissions\Nova;

use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsToMany;
use Pktharindu\NovaPermissions\Checkboxes;
use Pktharindu\NovaPermissions\Role as RoleModel;

class Role extends Resource
{
    public static $model = RoleModel::class;

    public static function group()
    {
        return __(config('nova-permissions.role_resource_group', 'Other'));
    }

    public static $title = 'name';

    public static $search = [
        'id',
        'slug',
        'name',
    ];

    public static $with = [
        'users',
    ];

    public function actions(NovaRequest $request)
    {
        return [];
    }

    public function cards(NovaRequest $request)
    {
        return [];
    }

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make(__('Name'), 'name')
                ->rules('required')
                ->sortable(),

            Slug::make(__('Slug'), 'slug')
                ->from('name')
                ->rules('required')
                ->creationRules('unique:' . config('nova-permissions.table_names.roles', 'roles'))
                ->updateRules('unique:' . config('nova-permissions.table_names.roles', 'roles') . ',slug,{{resourceId}}')
                ->sortable()
                ->hideFromIndex(),

            Checkboxes::make(__('Permissions'), 'permissions')
                ->withGroups()
                ->options(collect(config('nova-permissions.permissions'))->map(function ($permission, $key) {
                    return [
                        'group'        => __($permission['group']),
                        'option'       => $key,
                        'label'        => __($permission['display_name']),
                        'description'  => __($permission['description']),
                    ];
                })->groupBy('group')->toArray()),

            Text::make(__('Users'), function () {
                return \count($this->users);
            })->onlyOnIndex(),

            BelongsToMany::make(__('Users'), 'users', config('nova-permissions.user_resource', 'App\Nova\User'))
                ->searchable(),
        ];
    }

    public function filters(NovaRequest $request)
    {
        return [];
    }

    public static function label()
    {
        return __('Roles');
    }

    public function lenses(NovaRequest $request)
    {
        return [];
    }

    public static function singularLabel()
    {
        return __('Role');
    }
}
