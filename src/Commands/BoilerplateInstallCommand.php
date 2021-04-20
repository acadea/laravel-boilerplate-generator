<?php

namespace Acadea\Boilerplate\Commands;

use Acadea\Boilerplate\Utils\Composer;
use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Artisan;

class BoilerplateInstallCommand extends GeneratorCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boilerplate:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialise the required files for the boilerplate to work.';


    protected function getStub()
    {
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // TODO: complete this.

//        // composer install packages
        $packages = [
            'spatie/laravel-query-builder',
            'laravel/scout',
            'teamtnt/laravel-scout-tntsearch-driver',
            'spatie/laravel-permission',
        ];
//
        dump('Installing composer packages..');
        $this->laravel->make(Composer::class)->run(['require', ...$packages]);

//        collect($packages)->each(function ($package) {
//            $this->laravel->make(Composer::class)->run(['require', $package]);
//        });

        // composer install

        // TODO: create a dummy structure.php file as well
        // load stub and push to exception folder
        $stubs = [
            '/stubs/preload/json.exception.stub'             => '/app/Exceptions/GeneralJsonException.php',
            '/stubs/preload/baserepository.stub'             => '/app/Repositories/BaseRepository.php',
            '/stubs/preload/trait.disable-foreign-keys.stub' => '/database/seeders/Traits/DisableForeignKeys.php',
            '/stubs/preload/trait.truncate-table.stub'       => '/database/seeders/Traits/TruncateTable.php',
            '/stubs/preload/factory.helpers.stub'            => '/database/factories/helpers/FactoryHelper.php',
            '/stubs/preload/factory.user.stub'               => '/database/factories/UserFactory.php',
            '/stubs/preload/factory.role.stub'               => '/database/factories/RoleFactory.php',
            '/stubs/preload/factory.permission.stub'         => '/database/factories/PermissionFactory.php',
            '/stubs/preload/test.api-test-case.stub'         => '/tests/ApiTestCase.php',
            '/stubs/preload/test.test-case.stub'             => '/tests/TestCase.php',
            '/stubs/preload/model.role.stub'                 => '/app/Models/Role.php',
            '/stubs/preload/model.permission.stub'           => '/app/Models/Permission.php',
            '/stubs/preload/api.v1.root.stub'                => '/routes/api/v1.php',
            '/stubs/preload/route-helpers.stub'              => '/app/Helpers/Routes/RouteHelper.php',

        ];

        collect($stubs)->each(function ($dest, $source) {
            $this->publishStub($source, $dest);
        });

        dump('publishing laravel permission migration files and config');
        Artisan::call('vendor:publish', [
            '--provider' => "Spatie\Permission\PermissionServiceProvider",
        ]);

        dump('running migration fresh');
        Artisan::call('migrate:fresh');
    }

    public function publishStub($source, $dest)
    {
        $stubPath = file_exists($customPath = $this->laravel->basePath(trim($source, '/')))
            ? $customPath
            : __DIR__ . '/..' . $source;

        $stubContent = $this->files->get($stubPath);

        $path = $this->laravel->basePath($dest);

        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($stubContent));
    }
}
