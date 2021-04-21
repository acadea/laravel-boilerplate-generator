<?php

namespace Acadea\Boilerplate\Commands;

use Acadea\Boilerplate\Commands\Traits\ParseModel;
use Acadea\Boilerplate\Commands\Traits\ResolveStubPath;
use Acadea\Boilerplate\Utils\DataType;
use Acadea\Boilerplate\Utils\SchemaStructure;
use Acadea\Fixer\Facade\Fixer;
use Illuminate\Support\Str;
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

    public function handle()
    {
        return tap(parent::handle(), fn ($result) => dump("Created Factory {$this->qualifyClass($this->getNameInput())}"));
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

        $replace = [
            'DummyModel' => $model,
            '{{ model }}' => $model,
            '{{model}}' => $model,
            '{{ body }}' => $factoryFields,
            '{{body}}' => $factoryFields,
        ];

        $stub = $this->files->get($this->getStub());

        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        return Fixer::format(str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        ));
    }

    private function generateFactoryFields()
    {
        // read from schema structure
        $structure = SchemaStructure::get();

        // get model name
        $model = strtolower(Str::snake(Str::camel(Str::singular($this->getModelName()))));

        // find the fields from structure
        $fields = data_get($structure, strtolower($model));

        // generate faker
        return collect($fields)
            ->filter(fn ($field) => data_get($field, 'type') !== 'pivot')
            ->map(function ($field, $name) {
                return "'{$name}' => " . $this->generateFakerField(data_get($field, 'type'), $name) . ',';
            })->join("\n");
    }

    protected function generateFakerField($dataType, $fieldName)
    {
        // field name is something like 'name' or 'post_id'
        switch (DataType::standardise($dataType)) {
            case 'foreignId':
                $model = substr($fieldName, 0, -3);
                $model = Str::studly(Str::camel($model));

                return '\Database\Factories\Helpers\FactoryHelper::getRandomModelId(\App\Models\\' . $model . '::class)';
            case 'intArrays':
                return '[]';
            case 'integer':
                return '$this->faker->numberBetween(1,100)';
            case 'boolean':
                return '\Illuminate\Support\Arr::random([true, false])';
            case 'string':
                return '$this->faker->word()';
            case 'date':
                return 'now()->subDays(rand(1,20))';
            case 'float':
                return '$this->faker->randomFloat(3)';
            case 'text':
                return '$this->faker->text()';
            case 'timestamp':
                return 'now()->subDays(rand(1,20))';
            case 'ipAddress':
                return '$this->faker->ipv4';
            case 'json':
                return '$this->faker->words(3)';
            case 'macAddress':
                return '$this->faker->macAddress';
            case 'uuid':
                return '$this->faker->uuid';
            case 'year':
                return '$this->faker->year';
            default:
                return '\'\'';

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
            ['force', 'f', InputOption::VALUE_OPTIONAL, 'force to generate'],
        ];
    }
}
