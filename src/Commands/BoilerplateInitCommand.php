<?php

namespace Acadea\Boilerplate\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class BoilerplateInitCommand extends Command
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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // TODO: complete this.

    }


}
