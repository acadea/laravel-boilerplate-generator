<?php

namespace Acadea\Boilerplate\Commands;

use Acadea\Boilerplate\Commands\Traits\ParseModel;
use Acadea\Boilerplate\Utils\SchemaStructure;
use Acadea\Fixer\Facade\Fixer;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class ApiResourceMakeCommand extends \Illuminate\Foundation\Console\ResourceMakeCommand
{
    use ParseModel;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return file_exists($customPath = $this->laravel->basePath(trim('/stubs/api-resource.stub', '/')))
            ? $customPath
            : __DIR__.'/../stubs/api-resource.stub';
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boilerplate:resource {name} 
        {--model= : The model that this repo based on.} 
        {--force : Force to regenerate test file}
        {--c|collection : Create a resource collection.}
        ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Api Resource Class';

    protected $type = 'Resource';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        return tap(parent::handle(), fn($result) => dump("Created Resource {$this->qualifyClass($this->getNameInput())}"));
    }


    public function buildClass($name)
    {
        $replace = [
            '{{ fields }}' => $this->generateResourceFields(),
            '{{fields}}' => $this->generateResourceFields(),
        ];
        $replace = $this->buildModelReplacements($replace);

        return Fixer::format(str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        ));
    }


    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->getModelName());

        if (! class_exists($modelClass)) {
            throw new ModelNotFoundException('Model does not exist. Namespace: ' . $modelClass);
        }
        return array_merge($replace, [
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
    }

    protected function generateResourceFields()
    {
        // get all schema fields
        $structure = SchemaStructure::get();

        // get current model
        $model = strtolower(Str::snake(Str::camel(Str::singular($this->getModelName()))));

        $fields = data_get($structure, $model);

        return collect($fields)
            ->map(function ($fieldAttributes, $fieldName){
                return "'{$fieldName}' => data_get(\$this, '{$fieldName}'),";
            })
            ->join("\n");
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that this repository is based on.'],
            ['force', 'f', InputOption::VALUE_OPTIONAL, 'Force to regenerate test file.'],
            ['collection', 'c', InputOption::VALUE_NONE, 'Create a resource collection'],
        ];
    }
}
