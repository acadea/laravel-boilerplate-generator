<?php


namespace Acadea\Boilerplate\Tests\Features\Model;

use Acadea\Boilerplate\Tests\Helpers\StringHelper;
use Acadea\Boilerplate\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ModelTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->beforeApplicationDestroyed(function () {
            File::delete($this->app->path('Models/Post.php'));
        });
    }


    public function test_model_generated_is_correct()
    {
        Artisan::call('boilerplate:model', [
            'name' => 'Post',
            '--force' => true,
        ]);

        $generated = File::get($this->app->path('Models/Post.php'));

        $generated = StringHelper::clean($generated);

        $sourceOfTruth = File::get(self::TEST_ASSERT_FILES_PATH . '/Post.php.stub');

        $sourceOfTruth = StringHelper::clean($sourceOfTruth);

        $this->assertSame($sourceOfTruth, $generated);
    }

//    public function test_all_option_will_generate_all_the_boilerplate_files()
//    {
//        Artisan::call('boilerplate:model', [
//            'name' => 'Post',
//            '--force' => true,
//            '--api'=> true,
//            '--all' => true,
//        ]);
//
//        // verify there is a seeder
//        $seederPath = $this->app->path('../database/factories/PostFactory.php');
//
//        // verify there is a repository
//        $repositoryPath = $this->app->path('Repositories/Api/V1/PostRepository.php');
//
//        // verify there are events
//        $eventPaths = $this->app->path('Events');
//
//        // verify there is a factory
//
//        // verify there is a controller
//
//    }
}
