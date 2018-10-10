<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/8/18
 * Time: 6:07 PM
 */

namespace Mallabee\Queue\Core;

interface QueueConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return QueueInterface
     */
    public function connect(array $config);
}