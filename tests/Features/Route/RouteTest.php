<?php


namespace Acadea\Boilerplate\Tests\Features\Route;

use Acadea\Boilerplate\Tests\Helpers\StringHelper;
use Acadea\Boilerplate\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class RouteTest extends TestCase
{
    protected $mockFilePath = '../routes/api/v1/post.php';
    // test controller generated is ok
    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('boilerplate:model', [
            'name' => 'Post',
        ]);
        require $this->app->path('Models/Post.php');
        Artisan::call('boilerplate:route', [
            'name' => 'post',
            '--model' => 'Post'
        ]);

        $this->beforeApplicationDestroyed(function () {
            File::delete($this->app->path($this->mockFilePath));
        });
    }

    public function test_generated_route_is_correct()
    {
        $path = $this->app->path($this->mockFilePath);
        $file = File::get($path);

        $sourceOfTruth = File::get(self::TEST_ASSERT_FILES_PATH . '/post-routes.php.stub');
        // verify generated file is the same as source of truth
        $this->assertEquals(StringHelper::clean($sourceOfTruth), StringHelper::clean($file), 'not the same as known truth');
    }
}
