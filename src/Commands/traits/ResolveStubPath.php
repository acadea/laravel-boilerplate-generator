<?php


namespace Acadea\Boilerplate\Commands\traits;


trait ResolveStubPath
{
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__. '/../..'.$stub;
    }
}
