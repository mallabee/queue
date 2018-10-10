<?php

namespace Mallabee\Queue\Exceptions;

use InvalidArgumentException;

class NoConnectionFound extends InvalidArgumentException
{
    /**
     * Create a new exception instance.
     *
     * @param  string|null  $message
     * @return void
     */
    public function __construct($message = null)
    {
        parent::__construct($message);
    }
}
