<?php

namespace Acadea\Boilerplate\Tests;

use Acadea\Boilerplate\BoilerplateServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Acadea\\Boilerplate\\Tests\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

    }

    protected function getPackageProviders($app)
    {
        return [
            BoilerplateServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // set test-schema to boilerplate-generator/vendor/orchestra/testbench-core/laravel/tests/files/test-schema.php
        $app['config']->set('boilerplate.paths.schema-structure', '/../../../../tests/files/test-schema.php');



        /*
        include_once __DIR__.'/../database/migrations/create_skeleton_tables.php.stub';
        (new CreatePackageTables())->up();
        */
    }
}
