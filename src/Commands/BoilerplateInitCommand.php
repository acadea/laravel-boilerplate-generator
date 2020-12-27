<?php

namespace Acadea\Boilerplate\Commands;

use Acadea\Boilerplate\Utils\Composer;
use Acadea\Boilerplate\Utils\SchemaStructure;
use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class BoilerplateInitCommand extends GeneratorCommand
{
    protected function getStub()
    {

    }


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boilerplate:init ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the recommended boilerplate on based on configured schema.php.';



    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $schemas = SchemaStructure::get();

        $table = 'pivot:post_tag';

//        Artisan::call('boilerplate:migration', [
//            'name'     => "create_{$table}_pivot_table",
//            '--create' => $table,
//        ]);
//        return ;
        foreach ($schemas as $schema => $fields){

            if( substr(strtolower($schema),0, 6) === 'pivot:'){

                //  only run migration
                Artisan::call('boilerplate:migration', [
                    'name'     => "create_{$table}_pivot_table",
                    '--create' => $table,
                ]);
                continue;
            }

            Artisan::call('boilerplate:model', [
                'name' => Str::studly($schema),
                '--all' => true,
//                '--force' => $this->option('force'),
                '--force' => true,
                '--api' => true,
            ]);

        }

    }


}
