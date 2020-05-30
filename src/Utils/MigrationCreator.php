<?php


namespace Acadea\Boilerplate\Utils;

use Illuminate\Support\Str;
use League\Flysystem\FileNotFoundException;

class MigrationCreator extends \Illuminate\Database\Migrations\MigrationCreator
{

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

        return $stub;
    }


    protected function generateFieldsString($table)
    {
        // To read the file where the schema structure is defined.
//        if(!file_exists(config('boilerplate.paths.schema-structure'))){
//            throw new FileNotFoundException('Schema structure file not found. Please define the path to schema structure in config.');
//        }
//        $structure = require config('boilerplate.paths.schema-structure');

        $structure = [
            'post' => [
                'title' => [
                    'type' => 'string',
                    'attributes' => ['nullable'],
                ],
                'body' => [
                    'type' => 'mediumText',
                    'attributes' => ['nullable'],
                ],
                'author_id' => [
                    'type' => 'foreignId',
                    'foreign' => [
                        'references' => 'id',
                        'on' => 'authors',
                    ],
                ],

            ],
        ];

        dump($table);

        $fields = data_get($structure, strtolower(Str::singular($table)));

        $strings = collect($fields)->map(function ($props, $fieldName) {
            $fieldName = Str::snake(Str::camel($fieldName));
            $payload = '$table->'.data_get($props, 'type') . '(\'' . $fieldName . '\')';

            $attributes = data_get($props, 'attributes');


            if (is_array($attributes)) {
                foreach ($attributes as $attribute) {
                    $payload .= '->'.$attribute.'()';
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
        dump($strings);

        return implode("\n", array_values($strings->toArray()));
    }
}
