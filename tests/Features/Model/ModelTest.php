<?php


namespace Acadea\Boilerplate\Tests\Features\Model;


use Acadea\Boilerplate\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

class ModelTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }


    public function test_model_generated_is_correct()
    {
        Artisan::call('boilerplate:model Post', [
            '--force' => true,
        ]);
        

    }

    public function test_all_option_will_generate_all_the_boilerplate_files()
    {

    }

}
