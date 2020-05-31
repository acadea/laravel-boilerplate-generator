<?php

namespace Acadea\Boilerplate\Commands;

use Illuminate\Console\Command;

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

        // create exception class -- GeneralJsonException

        // create db trait: truncate table and disable/enable foreign key

        // create base repository

        // create base crud repository

    }
}
