<?php

namespace Acadea\Boilerplate\Commands;

use Acadea\Boilerplate\Utils\Composer;
use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;

class BoilerplateInitCommand extends GeneratorCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boilerplate:init';

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
            'laravel/passport',
            'laravel/scout',
            'teamtnt/laravel-scout-tntsearch-driver',
            'spatie/laravel-permission',
        ];
//
//        dump('Installing composer packages..');
//        $this->laravel->make(Composer::class)->run(['require', collect($packages)->join(' ')]);

//        collect($packages)->each(function ($package) {
//            $this->laravel->make(Composer::class)->run(['require', $package]);
//        });

        // composer install

        // TODO: create a dummy structure.php file as well
        // load stub and push to exception folder
        $stubs = [
            '/stubs/preload/json.exception.stub' => '/app/Exceptions/GeneralJsonException.php',
            '/stubs/preload/baserepository.stub' => '/app/Repositories/BaseRepository.php',
            '/stubs/preload/trait.disable-foreign-keys.stub' => '/database/seeds/Traits/DisableForeignKeys.php',
            '/stubs/preload/trait.truncate-table.stub' => '/database/seeds/Traits/TruncateTable.php',
            '/stubs/preload/factory.helpers.stub' => '/database/factories/helpers/FactoryHelper.php',
            '/stubs/preload/test.api-test-case.stub' => '/tests/ApiTestCase.php',
        // traits

        ];

        collect($stubs)->each(function ($dest, $source) {
            $this->publishStub($source, $dest);
        });
    }

    public function publishStub($source, $dest)
    {
        $stubPath = file_exists($customPath = $this->laravel->basePath(trim($source, '/')))
            ? $customPath
            : __DIR__. '/..' . $source;

        $stubContent = $this->files->get($stubPath);

        $path = $this->laravel->basePath($dest);

        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($stubContent));
    }
}
