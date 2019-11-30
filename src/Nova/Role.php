<?php

namespace Pktharindu\NovaPermissions\Nova;

use Laravel\Nova\Resource;
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
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = RoleModel::class;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static function group()
    {
        return config('nova-permissions.role_resource_group', 'Other');
    }

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'slug',
        'name',
    ];

    public static $with = [
        'users',
    ];

    /**
     * Get the actions available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
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
                ->creationRules('unique:roles')
                ->updateRules('unique:roles,slug,{{resourceId}}')
                ->sortable(),

            Checkboxes::make(__('Permissions'), 'permissions')
                ->withGroups()
                ->options(collect(config('nova-permissions.permissions'))->map(function ($permission, $key) {
                    return [
                        'group'        => ucfirst($permission['group']),
                        'option'       => $key,
                        'label'        => $permission['display_name'],
                        'description'  => $permission['description'],
                    ];
                })->groupBy('group')->toArray()),

            Text::make(__('Users'), function () {
                return \count($this->users);
            })->onlyOnIndex(),

            BelongsToMany::make(__('Users'), 'users', config('nova-permissions.user_resource', 'App\Nova\User'))
                ->searchable(),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    public static function label()
    {
        return __('Roles');
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    public static function singularLabel()
    {
        return __('Role');
    }
}
