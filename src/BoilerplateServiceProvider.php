<?php

namespace Acadea\Boilerplate;

use Acadea\Boilerplate\Commands\ApiEventMakeCommand;
use Acadea\Boilerplate\Commands\ApiResourceMakeCommand;
use Acadea\Boilerplate\Commands\ApiRouteMakeCommand;
use Acadea\Boilerplate\Commands\BoilerplateInitCommand;
use Acadea\Boilerplate\Commands\BoilerplateInstallCommand;
use Acadea\Boilerplate\Commands\ControllerMakeCommand;
use Acadea\Boilerplate\Commands\FactoryMakeCommand;
use Acadea\Boilerplate\Commands\MigrateMakeCommand;
use Acadea\Boilerplate\Commands\ModelMakeCommand;
use Acadea\Boilerplate\Commands\RepositoryMakeCommand;
use Acadea\Boilerplate\Commands\SeederMakeCommand;
use Acadea\Boilerplate\Commands\TestMakeCommand;
use Acadea\Boilerplate\Utils\MigrationCreator;
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
                __DIR__ . '/../src/stubs/schema.stub' => database_path('structure/schema.php'),
            ], 'database');


//            $this->publishes([
//                __DIR__.'/../resources/views' => base_path('resources/views/vendor/boilerplate'),
//            ], 'views');

//            if (! class_exists('CreatePackageTables')) {
//                $this->publishes([
//                    __DIR__ . '/../database/migrations/create_boilerplate_tables.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_boilerplate_tables.php'),
//                ], 'migrations');
//            }

            $this->commands([
                BoilerplateInitCommand::class,
                BoilerplateInstallCommand::class,
                RepositoryMakeCommand::class,
                ApiEventMakeCommand::class,
                ApiResourceMakeCommand::class,
                ModelMakeCommand::class,
                ApiRouteMakeCommand::class,
                MigrateMakeCommand::class,
                ControllerMakeCommand::class,
                FactoryMakeCommand::class,
                SeederMakeCommand::class,
                TestMakeCommand::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'boilerplate');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/boilerplate.php', 'boilerplate');
        $this->registerCreator();
    }

    public function registerCreator()
    {
        $this->app->singleton(MigrationCreator::class, function ($app) {
            $stubPath = file_exists($customPath = $app->basePath(trim('stubs', '/')))
                ? $customPath
                : __DIR__. '/stubs';

            return new MigrationCreator($app['files'], $stubPath);
        });
    }
}
