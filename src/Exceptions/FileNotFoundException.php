<?php


namespace Acadea\Boilerplate\Exceptions;


use Exception;
use Throwable;

class FileNotFoundException extends Exception implements Throwable
{
    protected $code = 500;

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        //
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
