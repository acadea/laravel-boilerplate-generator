<?php


namespace Acadea\Boilerplate\Exceptions;

use Exception;
use Throwable;

class AttributeValueMustBeArrayException extends Exception implements Throwable
{
    protected $code = 500;

    protected $message = 'Non keyed attribute must use an array as its value.';
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        return;
    }


    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        //
    }
}
