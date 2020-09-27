<?php

namespace Acadea\Boilerplate\Commands;

use Acadea\Boilerplate\Commands\traits\ParseModel;
use Acadea\Boilerplate\Commands\traits\ResolveStubPath;
use Acadea\Fixer\Facade\Fixer;
use Illuminate\Support\Str;

class SeederMakeCommand extends \Illuminate\Database\Console\Seeds\SeederMakeCommand
{
    use ResolveStubPath, ParseModel;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'boilerplate:seeder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new seeder class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Seeder';

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        $model = $this->getModelName();

        $tableName = Str::snake(Str::plural(Str::camel($model)));

        $replace = [
            'DummyModel' => $model,
            '{{ model }}' => $model,
            '{{model}}' => $model,
            '{{tableName}}' => $tableName,
            '{{ tableName }}' => $tableName,
        ];

        return Fixer::format(str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        ));
    }
}
