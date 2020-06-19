<?php

namespace Acadea\Boilerplate\Commands;

use Acadea\Boilerplate\Commands\traits\ParseModel;
use Acadea\Boilerplate\Commands\traits\ResolveStubPath;
use Acadea\Boilerplate\Utils\DataType;
use Acadea\Boilerplate\Utils\SchemaStructure;
use Symfony\Component\Console\Input\InputOption;

class FactoryMakeCommand extends \Illuminate\Database\Console\Factories\FactoryMakeCommand
{
    use ParseModel,
        ResolveStubPath;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'boilerplate:factory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model factory';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Factory';


    protected function getModelName()
    {
        $controller = $this->argument('name');

        $model = substr($controller, 0, strlen($controller) - strlen($this->type));

        return lcfirst(class_basename($this->parseModel($model)));
    }


    /**
     * Build the class with the given name.
     *
     * @param string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $model = ucfirst($this->getModelName());

        $factoryFields = $this->generateFactoryFields();

        // dd($model);
        $replace = [
            'DummyModel' => $model,
            '{{ model }}' => $model,
            '{{model}}' => $model,
            '{{ body }}' => $factoryFields,
            '{{body}}' => $factoryFields,
        ];

        $stub = $this->files->get($this->getStub());

        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        );
    }

    private function generateFactoryFields()
    {
        // read from schema structure
        $structure = SchemaStructure::get();

        // get model name
        $model = $this->getModelName();

        // find the fields from structure
        $fields = data_get($structure, strtolower($model));

        // generate faker
        return collect($fields)->map(function ($field, $name) {
            return "'{$name}' => " . $this->generateFakerField(data_get($field, 'type')) . ',';
        })->join("\n");
    }

    protected function generateFakerField($dataType)
    {
        $model = ucfirst($this->getModelName());
        switch (DataType::standardise($dataType)) {
            case 'foreignId':
                return '\Factories\Helpers\getRandomModelId(\App\Models\\' . $model . '::class)';
            case 'integer':
                return '$faker->numberBetween(1,100)';
            case 'boolean':
                return '\Illuminate\Support\Arr::random([true, false])';
            case 'string':
                return '$faker->word()';
            case 'date':
                return 'now()->subDays(rand(1,20))';
            case 'float':
                return '$faker->randomFloat(3)';
            case 'text':
                return '$faker->text()';
            case 'timestamp':
                return 'now()->subDays(rand(1,20))';
            case 'ipAddress':
                return '$faker->ipv4';
            case 'json':
                return '$faker->words(3)';
            case 'macAddress':
                return '$faker->macAddress';
            case 'uuid':
                return '$faker->uuid';
            case 'year':
                return '$faker->year';
            default:
                return '';

        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The name of the model'],
        ];
    }
}
