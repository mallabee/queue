<?php

namespace Mallabee\Queue\Drivers\Beanstalkd;

use Mallabee\Queue\Core\QueueInterface;
use Pheanstalk\Connection;
use Pheanstalk\Pheanstalk;

/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/8/18
 * Time: 5:05 PM
 */

class BeanstalkdConnector implements \Mallabee\Queue\Core\QueueConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return QueueInterface
     */
    public function connect(array $config): QueueInterface
    {
        $retryAfter = $config['retry_after'] ?? Pheanstalk::DEFAULT_TTR;

        return new BeanstalkdDriver($this->pheanstalk($config), $config['queue'], $retryAfter, $config['block_for'] ?? 0);
    }

    /**
     * Create a Pheanstalk instance.
     *
     * @param  array  $config
     * @return \Pheanstalk\Pheanstalk
     */
    protected function pheanstalk(array $config)
    {
        return Pheanstalk::connect(
            $config['host'],
            $config['port'] ?? Pheanstalk::DEFAULT_PORT,
            $config['timeout'] ?? Connection::DEFAULT_CONNECT_TIMEOUT
        );
    }
}