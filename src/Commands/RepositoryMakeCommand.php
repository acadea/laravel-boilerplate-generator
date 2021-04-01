<?php

namespace Acadea\Boilerplate\Commands;

use Acadea\Boilerplate\Utils\SchemaStructure;
use Acadea\Fixer\Facade\Fixer;
use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class RepositoryMakeCommand extends GeneratorCommand
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return file_exists($customPath = $this->laravel->basePath(trim('/stubs/repository.stub', '/')))
            ? $customPath
            : __DIR__. '/../stubs/repository.stub';
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boilerplate:repository {name} {--model= : The model that this repo based on.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Repository';

    protected $type = 'Repository';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        return tap(parent::handle(), fn($result) => dump("Created Repository {$this->qualifyClass($this->getNameInput())}"));
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Repositories\Api\V1\\';
    }

    protected function getSchemaFields()
    {
        $structure = SchemaStructure::get();

        return data_get($structure, strtolower(Str::singular($this->modelVariable())));
    }

    public function replaceCreateFields()
    {
        $fields = $this->getSchemaFields();

        return collect($fields)->map(function ($field, $fieldName) {
            if (data_get($field, 'type') === 'pivot') {
                return '';
            }

            return '\'' . $fieldName . '\' => data_get($data, \'' . $fieldName . '\'),';
        })->join("\n");
    }

    public function replaceUpdateFields()
    {
        $fields = $this->getSchemaFields();

        return collect($fields)->map(function ($field, $fieldName) {
            if (data_get($field, 'type') === 'pivot') {
                return '';
            }

            return '\'' . $fieldName . '\' => data_get($data, \'' . $fieldName . '\') ?? $' . $this->modelVariable() .'->' . $fieldName . ',';
        })->join("\n");
    }

    public function replaceFilters()
    {
        $fields = $this->getSchemaFields();

        return collect($fields)
            ->filter(fn ($field) => data_get($field, 'type') !== 'pivot')
            ->keys()
            ->join('\', \'');
    }

    public function replaceManyToManySync()
    {
        $fields = $this->getSchemaFields();

        return collect($fields)
            ->filter(function ($field) {
                return data_get($field, 'type') === 'pivot';
            })
            ->map(function ($field, $fieldName) {
                $foreignKey = Str::plural(data_get($field, 'pivot.foreign_key'));

                return 'if(data_get($data, \'' . $foreignKey . '\')){' . "\n" .
                    '$' . $this->modelVariable() . '->' . $fieldName . '()->sync(data_get($data, \'' . $foreignKey . '\'));' . "\n" .
                    '}';
            })
            ->join("\n");
    }

    public function buildClass($name)
    {
        $replace = [
            '{{ createFields }}' => $this->replaceCreateFields(),
            '{{createFields}}' => $this->replaceCreateFields(),
            '{{ updateFields }}' => $this->replaceUpdateFields(),
            '{{updateFields}}' => $this->replaceUpdateFields(),
            '{{filters}}' => $this->replaceFilters(),
            '{{ filters }}' => $this->replaceFilters(),
            '{{manyToManySync}}' => $this->replaceManyToManySync(),
            '{{ manyToManySync }}' => $this->replaceManyToManySync(),
        ];

        $replace = array_merge($replace, $this->buildModelReplacements($replace));

        return Fixer::format(str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        ));
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

    protected function getModelName()
    {
        if (! $this->option('model')) {
            // get model from name
            $name = $this->getNameInput();

            throw_if(substr($name, -10) !== 'Repository', InvalidArgumentException::class, "Name should follow the convention: {model}Repository");

            return str_replace('Repository', "", $name);
        }

        return $this->option('model');
    }

    protected function modelClass()
    {
        return $this->parseModel($this->getModelName());
    }

    protected function modelVariable()
    {
        return lcfirst(class_basename($this->modelClass()));
    }
    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->modelClass();

        if (! class_exists($modelClass)) {
            throw new ModelNotFoundException('Model does not exist. Namespace: ' . $modelClass);
        }

        $modelVariable = $this->modelVariable();

        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ model }}' => class_basename($modelClass),
            '{{model}}' => class_basename($modelClass),
            'DummyModelVariable' => $modelVariable,
            '{{ modelVariable }}' => $modelVariable,
            '{{modelVariable}}' => $modelVariable,
        ]);
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
            ['force', 'f', InputOption::VALUE_OPTIONAL, 'Force to regenerate boilerplate.'],
        ];
    }
}
