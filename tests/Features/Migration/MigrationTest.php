<?php


namespace Acadea\Boilerplate\Tests\Features\Migration;

use Acadea\Boilerplate\Tests\Helpers\StringHelper;
use Acadea\Boilerplate\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MigrationTest extends TestCase
{

    protected $timePrefix;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('boilerplate:migration', [
            'name' => "create_posts_table",
            '--create' => 'posts',
        ]);

        Artisan::call('boilerplate:migration', [
            'name' => "create_pivot:post_tag_pivot_table",
            '--create' => 'pivot:post_tag',
        ]);

        $this->timePrefix = now()->format('Y_m_d_His');

        $this->beforeApplicationDestroyed(function () {
            File::deleteDirectory($this->app->path('../database/migrations/'));
        });
    }

    protected function getTimePrefix()
    {
        $prefix = $this->timePrefix;

        $exist = File::exists($this->app->path('../database/migrations/' . $prefix . '_create_posts_table.php'));

        if (! $exist) {
            return now()->clone()->subSecond()->format('Y_m_d_His');
        }

        return $prefix;
    }

    public function test_migration_file_content_is_correct()
    {
        $generated = File::get($this->app->path('../database/migrations/' . $this->getTimePrefix() . '_create_posts_table.php'));

        $source = File::get(self::TEST_ASSERT_FILES_PATH . '/post_migration.php.stub');

        $this->assertSame(StringHelper::clean($source), StringHelper::clean($generated));

        // test pivot migration file name

        $generated = File::get($this->app->path('../database/migrations/' . $this->getTimePrefix() . '_create_post_tag_pivot_table.php'));

        $source = File::get(self::TEST_ASSERT_FILES_PATH . '/post_tag_pivot_migration.php.stub');

        $this->assertSame(StringHelper::clean($source), StringHelper::clean($generated));
    }
}
