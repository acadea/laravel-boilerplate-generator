<?php


namespace Acadea\Boilerplate\Commands\Traits;

trait ResolveStubPath
{
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__. '/../..'.$stub;
    }
}
