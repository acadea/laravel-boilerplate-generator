<?php


namespace Acadea\Boilerplate\Tests\Features\Factory;

use Acadea\Boilerplate\Tests\Helpers\StringHelper;
use Acadea\Boilerplate\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class FactoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->beforeApplicationDestroyed(function () {
            File::delete($this->app->path('../database/factories/PostFactory.php'));
        });
    }

    public function test_factory_generated_is_the_same()
    {
        Artisan::call('boilerplate:factory', [
            'name' => "PostFactory",
            '--model' => 'Post',
        ]);

        $generated = File::get($this->app->path('../database/factories/PostFactory.php'));

        $source = File::get(self::TEST_ASSERT_FILES_PATH . '/PostFactory.php.stub');

        $this->assertSame(StringHelper::clean($source), StringHelper::clean($generated));
    }
}
