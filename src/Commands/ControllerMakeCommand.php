<?php

namespace Acadea\Boilerplate\Commands;

use Faker\Generator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use League\Flysystem\FileNotFoundException;

class ControllerMakeCommand extends \Illuminate\Routing\Console\ControllerMakeCommand
{
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

    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__. '/../'.$stub;
    }

//    protected function getStub()
//    {
//        return $this->resolveStubPath('/stubs/controller.api.with-docs.stub');
//    }

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


        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }

    public function generateIndexDocs()
    {
        return '';
    }

    protected function getModelName()
    {
        $controller = $this->argument('name');

        $model = substr($controller, 0, strlen($controller) - 10);

        return lcfirst(class_basename($this->parseModel($model)));
    }

    protected function getDbStructure()
    {
        // To read the file where the schema structure is defined.
        $structurePath = $this->laravel->basePath() . config('boilerplate.paths.schema-structure');
        if (! file_exists($structurePath)) {
            throw new FileNotFoundException('Schema structure file not found. Please define the path to schema structure in config.');
        }

        $model = $this->getModelName();

        $fields = data_get(require $structurePath, $model);

        throw_if($fields === null, ModelNotFoundException::class, 'Undefined model ' . $model);

        return $fields;
    }

    private function generateBodyParams($field, $value, $required = true)
    {
        // if attributes contain 'nullable'
        $nullable = collect(data_get($value, 'attribute'))->contains('nullable');

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
        })->join("\n*");
    }

    protected function generateExample($dataType)
    {
        $faker = $this->laravel->make(Generator::class);

        $integers = ['foreignId', 'bigInteger', 'integer', 'mediumInteger', 'smallInteger', 'tinyInteger', 'unsignedBigInteger', 'unsignedInteger', 'unsignedMediumInteger', 'unsignedSmallInteger', 'unsignedTinyInteger'];
        $booleans = ['binary', 'boolean'];
        $strings = ['char', 'string'];
        $dates = ['date', 'dateTime', 'dateTimeTz'];
        $timestamps = [ 'nullableTimestamps', 'time', 'timeTz', 'timestamp', 'timestampTz'];
        $floats = ['decimal', 'double', 'float', 'unsignedDecimal'];

        $paragraphs = ['longText', 'mediumText', 'text'];

        if (collect($integers)->contains($dataType)) {
            return 1;
        }
        if (collect($booleans)->contains($dataType)) {
            return Arr::random([true, false]);
        }
        if (collect($strings)->contains($dataType)) {
            return $faker->text;
        }
        if (collect($dates)->contains($dataType)) {
            return $faker->date();
        }
        if (collect($floats)->contains($dataType)) {
            return $faker->randomNumber(3);
        }
        if (collect($paragraphs)->contains($dataType)) {
            return $faker->paragraph(3);
        }
        if (collect($timestamps)->contains($dataType)) {
            return $faker->unixTime();
        }
        if ($dataType === 'ipAddress') {
            return $faker->ipv4;
        }
        if ($dataType === 'json') {
            return json_encode($faker->randomElements());
        }
        if ($dataType === 'macAddress') {
            return $faker->macAddress;
        }
        if ($dataType === 'uuid') {
            return Str::uuid()->toString();
        }
        if ($dataType === 'year') {
            return $faker->year;
        }

        return '';
    }

    public function generateUpdateDocs()
    {
        // grab all fields from model structure
        $fields = $this->getDbStructure();

        // for each field generate docs and example
        return collect($fields)->map(function ($value, $field) {
            return $this->generateBodyParams($field, $value, false);
        })->join("\n*");
    }

    public function generateShowDocs()
    {
        return '';
    }

    public function generateDeleteDocs()
    {
        return '';
    }

    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (! Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace.'Models\\'.$model;
        }

        return $model;
    }
}
