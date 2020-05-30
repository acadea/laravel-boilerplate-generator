<?php

namespace Acadea\Boilerplate;

use Acadea\Boilerplate\Commands\ApiEventMakeCommand;
use Acadea\Boilerplate\Commands\ApiRouteMakeCommand;
use Acadea\Boilerplate\Commands\ModelMakeCommand;
use Acadea\Boilerplate\Commands\RepositoryMakeCommand;
use Illuminate\Support\ServiceProvider;

class BoilerplateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/boilerplate.php' => config_path('boilerplate.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../resources/views' => base_path('resources/views/vendor/boilerplate'),
            ], 'views');

            if (! class_exists('CreatePackageTables')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_boilerplate_tables.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_boilerplate_tables.php'),
                ], 'migrations');
            }

            $this->commands([
                RepositoryMakeCommand::class,
                ApiEventMakeCommand::class,
                ModelMakeCommand::class,
                ApiRouteMakeCommand::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'boilerplate');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/boilerplate.php', 'boilerplate');
    }
}
