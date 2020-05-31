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
    /**
     * @var Composer
     */
    private Composer $composer;

    public function __construct(Composer $composer)
    {
        parent::__construct();
        $this->composer = $composer;
    }

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

        // composer install packages
        $packages = [
            'spatie/laravel-query-builder',
            'laravel/passport',
            'laravel/scout',
            'teamtnt/laravel-scout-tntsearch-driver',
            'spatie/laravel-permission',
        ];

        dump('Installing composer packages..');
        collect($packages)->each(function ($package){
            $this->composer->run(['require', $package]);
        });

        // composer install

        // create exception class -- GeneralJsonException
        // load stub and push to exception folder
        $path =
        $this->files->put($path, $this->sortImports($this->buildClass($name)));


        // create db trait: truncate table and disable/enable foreign key

        // create base repository

        // create base crud repository

        // create base api repository
    }

    public function buildJsonException()
    {
        $path = '';
    }
}
