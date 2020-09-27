<?php

namespace Acadea\Boilerplate\Commands;

use Acadea\Boilerplate\Commands\traits\ParseModel;
use Acadea\Fixer\Facade\Fixer;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class TestMakeCommand extends \Illuminate\Foundation\Console\TestMakeCommand
{
    use ParseModel;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return file_exists($customPath = $this->laravel->basePath(trim('/stubs/test.api.stub', '/')))
            ? $customPath
            : __DIR__.'/../stubs/test.api.stub';
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boilerplate:test {name} {--model= : The model that this repo based on.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Api Test Class';

    protected $type = 'Test';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        parent::handle();
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return 'Tests\Feature\Api\V1\\';
    }

    public function buildClass($name)
    {
        $replace = [];

        $replace = $this->buildModelReplacements($replace);

        return Fixer::format(str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        ));
    }


    protected function getModelName()
    {
        if (! $this->option('model')) {
            // get model from name
            $name = $this->getNameInput();
            ;
            throw_if(substr($name, -7) !== 'ApiTest', InvalidArgumentException::class, "Name should follow the convention: {model}ApiTest");

            return str_replace('ApiTest', "", $name);
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

        // make sure routename is plurarised correctly
        $endpointName = Str::kebab(Str::camel(Str::plural(class_basename($modelClass))));
        $routeEndpoint = substr($endpointName, 0, strlen($endpointName) - 1);

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
            '{{ routeEndpoint }}' => $routeEndpoint,
            '{{routeEndpoint}}' => $routeEndpoint,

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

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        $modelName = $this->getModelName();

        $path = str_replace('\\', '/', $name);

        $splitted = explode('/', $path);

        array_splice($splitted, -2, 0, $modelName);

        return base_path('tests').implode('/', $splitted). '.php';
    }
}
