<?php

namespace Acadea\Boilerplate\Tests\Features\Controller;

use Acadea\Boilerplate\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class ControllerTest extends TestCase
{
    // test controller generated is ok
    public function setUp(): void
    {
        parent::setUp();
        // generate a test controller based on fake model

        Artisan::call('boilerplate:controller PostController --api');
    }


    // test has index method
    public function test_has_index_method()
    {
        // FIXME: can't get this working
        // get controller instance
        require '../../../vendor/orchestra/testbench-core/laravel/app/Http/Controllers/Api/V1/PostController.php';
        $instance = $this->app->make('App\\Http\\Controllers\\Api\\V1');
        dd($instance);
        $reflection = (new \ReflectionClass('App\\Http\\Controllers\\Api\\V1'));
        $hasIndex = $reflection->hasMethod('index');
        $this->assertTrue($hasIndex);
    }

    // test has store method
    // test has update method
    // test has destroy method
    // test has show method

    // test doc

    // test store method



}
