<?php

namespace Acadea\Boilerplate\Commands;

use Acadea\Boilerplate\Commands\Traits\ParseModel;
use Acadea\Boilerplate\Commands\Traits\ResolveStubPath;
use Acadea\Boilerplate\Utils\DataType;
use Acadea\Boilerplate\Utils\SchemaStructure;
use Acadea\Fixer\Facade\Fixer;
use Faker\Generator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class ControllerMakeCommand extends \Illuminate\Routing\Console\ControllerMakeCommand
{
    use ParseModel, ResolveStubPath;
    /**
     * The console command name.
     *
     * @var string
     */
//    protected $signature = 'boilerplate:controller {name} {--api= : If target controller is an api controller.}';
    protected $name = 'boilerplate:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        if ($this->option('api')) {
            return $rootNamespace . '\Http\Controllers\Api\V1';
        }

        return $rootNamespace . '\Http\Controllers';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = '/stubs/controller.api.stub';

        return $this->resolveStubPath($stub);
    }

    public function handle()
    {
        return tap(parent::handle(), fn ($result) => dump("Created Controller {$this->qualifyClass($this->getNameInput())}"));
    }


    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $modelClass = $this->parseModel(ucfirst($this->getModelName()));

        $replace = [

            '{{ indexDocs }}' => $this->generateIndexDocs(),
            '{{indexDocs}}' => $this->generateIndexDocs(),

            '{{ storeDocs }}' => $this->generateStoreDocs(),
            '{{storeDocs}}' => $this->generateStoreDocs(),

            '{{ showDocs }}' => $this->generateShowDocs(),
            '{{showDocs}}' => $this->generateShowDocs(),

            '{{ updateDocs }}' => $this->generateUpdateDocs(),
            '{{updateDocs}}' => $this->generateUpdateDocs(),

            '{{ deleteDocs }}' => $this->generateDeleteDocs(),
            '{{deleteDocs}}' => $this->generateDeleteDocs(),

        ];

        $replace = array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ model }}' => class_basename($modelClass),
            '{{model}}' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
            '{{modelVariable}}' => lcfirst(class_basename($modelClass)),
        ]);


        return Fixer::format(str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        ));
    }

    public function generateIndexDocs()
    {
        return '';
    }

    protected function getModelName()
    {
        $controller = $this->argument('name');

        $model = substr($controller, 0, strlen($controller) - strlen($this->type));

        return lcfirst(class_basename($this->parseModel($model)));
    }

    protected function getDbStructure()
    {
        $model = $this->getModelName();

        $model = Str::snake(Str::camel(Str::singular($model)));

        // To read the file where the schema structure is defined.
        $fields = data_get(SchemaStructure::get(), $model);

        throw_if($fields === null, ModelNotFoundException::class, 'Undefined model ' . $model);

        return $fields;
    }

    private function generateBodyParams($field, $value, $required = true)
    {
        // if attributes contain 'nullable'
        $nullable = collect(data_get($value, 'attributes'))->contains('nullable');

        $doc = '@bodyParam ' . $field;

        // add required filed
        $doc .= $nullable && $required ? ' ' : ' required ';

        // add data type
        $doc .= data_get($value, 'type');

        // add desc
        $doc .= ' ' . ucfirst($this->getModelName()) . ' ' . str_replace('_', ' ', $field) . '. ';

        // add example
        $doc .= 'Example: ' . $this->generateExample(data_get($value, 'type'));

        return $doc;
    }

    public function generateStoreDocs()
    {
        // grab all field from model structure
        $fields = $this->getDbStructure();

        // for each field generate docs and example
        return collect($fields)->map(function ($value, $field) {
            return $this->generateBodyParams($field, $value, true);
        })->join("\n     * ");
    }

    protected function generateExample($dataType)
    {
        $faker = $this->laravel->make(Generator::class);
        $faker->seed(1);

        $dataType = DataType::standardise($dataType);

        switch ($dataType) {
            case 'integer':
                return 1;
            case 'foreignId':
                return 1;
            case 'boolean':
                return true;
            case 'string':
                return $faker->text(20);
            case 'date':
                return $faker->date();
            case 'float':
                return $faker->randomNumber(3);
            case 'text':
                return $faker->text(80);
            case 'timestamp':
                return $faker->unixTime();
            case 'ipAddress':
                return $faker->ipv4;
            case 'json':
                return json_encode($faker->words(3));
            case 'macAddress':
                return $faker->macAddress;
            case 'uuid':
                return $faker->uuid;
            case 'year':
                return $faker->year;
            case 'intArrays':
                return '[1, 2]';
            default:
                return '';
        }
    }

    public function generateUpdateDocs()
    {
        // grab all fields from model structure
        $fields = $this->getDbStructure();

        // for each field generate docs and example
        return collect($fields)->map(function ($value, $fieldName) {
            return $this->generateBodyParams($fieldName, $value, false);
        })->join("\n     * ");
    }

    public function generateShowDocs()
    {
        return '';
    }

    public function generateDeleteDocs()
    {
        return '';
    }
}
