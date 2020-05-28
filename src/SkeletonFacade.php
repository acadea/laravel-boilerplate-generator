<?php

namespace Acadea\Boilerplate;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Acadea\Boilerplate\Boilerplate
 */
class BoilerplateFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'skeleton';
    }
}
