<?php

namespace Mallabee\Queue\Core;

use Exception;

interface ExceptionHandlerInterface
{
    /**
     * Report or log an exception.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e);
}
