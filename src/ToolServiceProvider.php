<?php

namespace Pktharindu\NovaPermissions;

use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(Filesystem $filesystem)
    {
        $this->publishes([
            __DIR__.'/../config/nova-permissions.php' => config_path('nova-permissions.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/create_gates_table.php.stub' => $this->getMigrationFileName($filesystem),
        ], 'migrations');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/nova-permissions.php',
            'nova-permissions'
        );
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param Filesystem $filesystem
     *
     * @return string
     */
    protected function getMigrationFileName(Filesystem $filesystem): string
    {
        $timestamp = date('Y_m_d_His');

        return Collection::make($this->app->databasePath().\DIRECTORY_SEPARATOR.'migrations'.\DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem) {
                return $filesystem->glob($path.'*_create_gates_table.php');
            })->push($this->app->databasePath()."/migrations/{$timestamp}_create_gates_table.php")
            ->first();
    }
}
