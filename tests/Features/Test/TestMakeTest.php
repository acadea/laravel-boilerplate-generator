<?php


namespace Acadea\Boilerplate\Tests\Features\Test;

use Acadea\Boilerplate\Tests\Helpers\StringHelper;
use Acadea\Boilerplate\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class TestMakeTest extends TestCase
{
    protected $mockFilePath = '../tests/Feature/Api/V1/Post/PostApiTest.php';

    // test controller generated is ok
    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('boilerplate:test', [
            'name' => 'PostApiTest',
            '--force' => true,
        ]);

        $this->beforeApplicationDestroyed(function () {
            File::delete($this->app->path($this->mockFilePath));
        });
    }

    public function test_generated_test_is_correct()
    {
        $path = $this->app->path($this->mockFilePath);
        $file = File::get($path);

        $sourceOfTruth = File::get(self::TEST_ASSERT_FILES_PATH . '/PostApiTest.php.stub');
        // verify generated file is the same as source of truth
        $this->assertEquals(StringHelper::clean($sourceOfTruth), StringHelper::clean($file), 'not the same as known truth');
    }
}
