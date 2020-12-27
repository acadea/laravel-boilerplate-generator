<?php


namespace Acadea\Boilerplate\Utils;

use Acadea\Fixer\Facade\Fixer;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use League\Flysystem\FileNotFoundException;

class MigrationCreator extends \Illuminate\Database\Migrations\MigrationCreator
{
    private function isPivot($name)
    {
        return substr(strtolower($name), 0, 6) === 'pivot:';
    }
    /**
     * Get the migration stub file.
     *
     * @param  string|null  $table
     * @param  bool  $create
     * @return string
     */
    protected function getStub($table, $create)
    {
        $isPivot = $this->isPivot($table);

        if (is_null($table)) {
            $stub = $this->files->exists($customPath = $this->customStubPath.'/migration.stub')
                ? $customPath
                : $this->stubPath().'/migration.stub';
        } elseif ($create && ! $isPivot) {
            $stub = $this->files->exists($customPath = $this->customStubPath.'/migration.create.stub')
                ? $customPath
                : $this->stubPath().'/migration.create.stub';
        } elseif ($create && $isPivot) {
            $stub = $this->files->exists($customPath = $this->customStubPath.'/migration.pivot.stub')
                ? $customPath
                : $this->stubPath().'/migration.pivot.stub';
        } else {
            $stub = $this->files->exists($customPath = $this->customStubPath.'/migration.update.stub')
                ? $customPath
                : $this->stubPath().'/migration.update.stub';
        }

        return $this->files->get($stub);
    }

    /**
     * Get the full path to the migration.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getPath($name, $path)
    {
        $name = $this->removePivotFromName($name);

        return $path.'/'.$this->getDatePrefix().'_'.$name.'.php';
    }

    private function removePivotFromName(string $name)
    {
        // $name is something like: create_veges_table
        $splitted = explode('_', $name);
        $isPivot = $this->isPivot($splitted[1]);
        if ($isPivot) {
            $splitted[1] = substr($splitted[1], 6);
            $name = implode('_', $splitted);
        }

        return $name;
    }

    /**
     * Get the class name of a migration name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getClassName($name)
    {
        return Str::studly($this->removePivotFromName($name));
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string  $name
     * @param  string  $stub
     * @param  string|null  $table
     * @return string
     */
    protected function populateStub($name, $stub, $table)
    {
        $stub = str_replace(
            ['DummyClass', '{{ class }}', '{{class}}'],
            $this->getClassName($name),
            $stub
        );

        // Here we will replace the table place-holders with the table specified by
        // the developer, which is useful for quickly creating a tables creation
        // or update migration from the console instead of typing it manually.
        if (! is_null($table)) {
            $stub = str_replace(
                ['DummyTable', '{{ table }}', '{{table}}'],
                $table,
                $stub
            );


            $stub = str_replace(['{{fields}}', '{{ fields }}'], $this->generateFieldsString($table), $stub);
        }

        $stub = Fixer::format($stub);

        return $stub;
    }


    protected function generateFieldsString($table)
    {
        $schemaPath = App::basePath() . config('boilerplate.paths.schema-structure');
        // To read the file where the schema structure is defined.
        if (! file_exists($schemaPath)) {
            throw new FileNotFoundException('Schema structure file not found. Please define the path to schema structure in config.');
        }
        $structure = require $schemaPath;

        $fields = data_get($structure, strtolower(Str::singular($table)));

        $strings = collect($fields)->map(function ($props, $fieldName) {
            $fieldName = Str::snake(Str::camel($fieldName));

            // skipping pivot entry
            if (data_get($props, 'type') === 'pivot') {
                return '';
            }

            $payload = '$table->'.data_get($props, 'type') . '(' . var_export($fieldName, true) . ')';

            $attributes = data_get($props, 'attributes');

            if (is_array($attributes)) {
                foreach ($attributes as $key => $value) {
                    $arguments = '';
                    if (! is_integer($key) && is_array($value)) {
                        // has arguments passed to value
                        // turning arg into comma joined string
                        $value = array_map(fn ($val) => var_export($val, true), $value);
                        $arguments = implode(', ', $value);
                    }
                    $payload .= '->'.$value.'(' . $arguments .')';
                }
            }

            if (($foreign = data_get($props, 'foreign')) !== null) {
                $payload .= ';';
                $on = data_get($foreign, 'on');
                $references = data_get($foreign, 'references');
                $payload .= "\n" . '$table->foreign(\'' . $fieldName . '\')->on(\''.$on.'\')->references(\''.$references.'\')->cascadeOnDelete()';
            }

            return $payload . ';';
        });

        // filter all primary keys
        $primaryKeys = collect($fields)->filter(function ($field) {
            return data_get($field, 'primary');
        })->keys();

        if ($primaryKeys->isNotEmpty()) {
            // get all with
            $payload = '$table->primary([\'' . $primaryKeys->join('\', \'') . '\']);';
            $strings = $strings->add($payload);
        }

        return $strings->values()->join("\n");
    }
}
