<?php

namespace Acadea\Boilerplate\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class ApiEventMakeCommand extends GeneratorCommand
{

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return file_exists($customPath = $this->laravel->basePath(trim('/stubs/event.api.stub', '/')))
            ? $customPath
            : __DIR__. '/..' . '/stubs/event.api.stub';
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boilerplate:api-event {name} {--model= : The model that this event based on.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Api Event';

    protected $type = 'Event';

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Events\Models\\' . Str::ucfirst($this->getModelName()) ;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        return tap(parent::handle(), fn($result) => dump("Created Event {$this->qualifyClass($this->getNameInput())}"));
    }

    public function buildClass($name)
    {
        $replace = [];

        $replace = $this->buildModelReplacements($replace);

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
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

        return Str::ucfirst($model);
    }

    protected function getModelName()
    {
        if (! $this->option('model')) {
            // get model from name
            $name = $this->getNameInput();
            $exploded = explode('_', Str::snake(Str::camel($name)));
            $sliced = array_slice(array_map([Str::class, 'ucfirst'], $exploded), 0, sizeof($exploded) - 1);

            return implode('', $sliced);
        }

        return $this->option('model');
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

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that this repository is based on.'],
        ];
    }
}
