<?php

// Currently unused - Using \Illuminate\Contracts\Redis\Factory instead

namespace Mallabee\Queue\Drivers\Redis;

interface Factory
{
    /**
     * Get a Redis connection by name.
     *
     * @param  string  $name
     * @return \Mallabee\Queue\Drivers\Redis\Connection
     */
    public function connection($name = null);
}
