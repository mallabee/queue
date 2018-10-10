<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/9/18
 * Time: 9:42 AM
 */

namespace Mallabee\Queue\Core;

use Exception;

class NullExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * Report or log an exception.
     *
     * @param  \Exception $e
     * @return void
     */
    public function report(Exception $e)
    {
        //
    }
}