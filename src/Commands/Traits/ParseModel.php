<?php


namespace Acadea\Boilerplate\Commands\Traits;

use Illuminate\Support\Str;

trait ParseModel
{
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new \InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (! Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace.'Models\\'.$model;
        }

        return $model;
    }

    protected function getModelName()
    {
        $name = $this->argument('name');

        $model = substr($name, 0, strlen($name) - strlen($this->type));

        return class_basename($this->parseModel($model));
    }
}
