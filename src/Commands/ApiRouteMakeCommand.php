<?php

namespace Acadea\Boilerplate\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class ApiRouteMakeCommand extends GeneratorCommand
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = '/stubs/route.stub';

        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__. '/..' . $stub;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        $kebab = Str::kebab(Str::camel($name));

        return App::basePath().'/routes/api/v1/'.str_replace('\\', '/', $kebab).'.php';
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boilerplate:route {name} {--model= : The model that this repo based on.} {--force= : Force to regenerate api route file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Route';

    protected $type = 'Route';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        return tap(parent::handle(), fn ($result) => dump("Created Route {$this->qualifyClass($this->getNameInput())}"));
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

        return $model;
    }

    protected function getModelName()
    {
        if (! $this->option('model')) {
            // get model from name
            $name = $this->getNameInput();

            return Str::studly($name);
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

        // needs to be kebab cased
        $modelKebabPlural = strtolower(Str::kebab(Str::camel(Str::plural(class_basename($modelClass)))));

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
            '{{modelKebabPlural}}' => $modelKebabPlural,
            '{{ modelKebabPlural }}' => $modelKebabPlural,
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
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that this route is based on.'],
            ['force', 'f', InputOption::VALUE_OPTIONAL, 'Force to regenerate boilerplate.'],
        ];
    }
}
