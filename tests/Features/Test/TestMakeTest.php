<?php


namespace Acadea\Boilerplate\Tests\Features\Test;

use Acadea\Boilerplate\Tests\Helpers\StringHelper;
use Acadea\Boilerplate\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class TestMakeTest extends TestCase
{
    protected $mockFilePath = '../database/seeders/PostSeeder.php';

    // test controller generated is ok
    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('boilerplate:test', [
            'name' => 'PostSeeder',
        ]);

        $this->beforeApplicationDestroyed(function () {
            File::delete($this->app->path($this->mockFilePath));
        });
    }

    public function test_generated_seeder_is_correct()
    {
        $path = $this->app->path($this->mockFilePath);
        $file = File::get($path);

        $sourceOfTruth = File::get(self::TEST_ASSERT_FILES_PATH . '/PostSeeder.php.stub');
        // verify generated file is the same as source of truth
        $this->assertEquals(StringHelper::clean($sourceOfTruth), StringHelper::clean($file), 'not the same as known truth');
    }
}
