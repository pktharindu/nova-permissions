<?php

namespace Pktharindu\NovaPermissions\Nova;

use App\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Benjaminhirsch\NovaSlugField\Slug;
use Laravel\Nova\Fields\BelongsToMany;
use Pktharindu\NovaPermissions\Checkboxes;
use Benjaminhirsch\NovaSlugField\TextWithSlug;
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

    public function actions(Request $request)
    {
        return [];
    }

    public function cards(Request $request)
    {
        return [];
    }

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            TextWithSlug::make(__('Name'), 'name')
                ->rules('required')
                ->sortable()
                ->slug('slug'),

            Slug::make(__('Slug'), 'slug')
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

    public function filters(Request $request)
    {
        return [];
    }

    public static function label()
    {
        return __('Roles');
    }

    public function lenses(Request $request)
    {
        return [];
    }

    public static function singularLabel()
    {
        return __('Role');
    }
}
