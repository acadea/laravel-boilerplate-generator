<?php


namespace Acadea\Boilerplate\Tests\Features\Repository;


use Acadea\Boilerplate\Tests\Helpers\StringHelper;
use Acadea\Boilerplate\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class RepositoryTest extends TestCase
{
    // test controller generated is ok
    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('boilerplate:model', [
            'name' => 'Post'
        ]);
        require $this->app->path('Models/Post.php');
        Artisan::call('boilerplate:repository', [
            'name' => 'PostRepository',
        ]);

        $this->beforeApplicationDestroyed(function (){
            File::delete($this->app->path('Repository/Api/V1/PostRepository.php'));
        });
    }

    public function test_generated_repository_is_correct()
    {

        $path = $this->app->path('Repositories/Api/V1/PostRepository.php');
        $file = File::get($path);

        $sourceOfTruth = File::get(self::TEST_ASSERT_FILES_PATH . '/PostRepository.php.stub');
        // verify generated file is the same as source of truth
        $this->assertEquals(StringHelper::clean($sourceOfTruth), StringHelper::clean($file), 'not the same as known truth');
    }
}
