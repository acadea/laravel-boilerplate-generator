<?php


namespace Acadea\Boilerplate\Utils;

use Acadea\Boilerplate\Exceptions\FileNotFoundException;
use Illuminate\Support\Facades\App;

class SchemaStructure
{
    public static function get(): array
    {
        // get all fields defined in schema structure array
        $configExist = file_exists($customPath = App::basePath() . '/' . trim(config('boilerplate.paths.schema-structure'), '/'));
//            ? $customPath
//            : __DIR__. '/../stubs/schema.stub';

        if (! $configExist) {
            throw new FileNotFoundException('Schema structure file not found. Please define the path to schema structure in config.');
        }

        return require $customPath;
    }
}
