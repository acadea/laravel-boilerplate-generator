<?php

namespace Acadea\Boilerplate;

use Acadea\Boilerplate\Commands\RepositoryMakeCommand;
use Illuminate\Support\ServiceProvider;

class BoilerplateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/boilerplate.php' => config_path('skeleton.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views' => base_path('resources/views/vendor/skeleton'),
            ], 'views');

            if (! class_exists('CreatePackageTables')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_skeleton_tables.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_skeleton_tables.php'),
                ], 'migrations');
            }

            $this->commands([
                RepositoryMakeCommand::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'skeleton');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/boilerplate.php', 'skeleton');
    }
}
