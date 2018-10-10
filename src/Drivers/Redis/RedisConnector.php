<?php

namespace Mallabee\Queue\Drivers\Redis;

use Mallabee\Queue\Core\QueueConnectorInterface;
use Mallabee\Queue\Core\QueueInterface;

class RedisConnector implements QueueConnectorInterface
{
    /**
     * The Redis database instance.
     *
     * @var \Mallabee\Queue\Drivers\Redis\Factory
     */
    protected $redis;

    /**
     * The connection name.
     *
     * @var string
     */
    protected $connection;

    /**
     * Create a new Redis queue connector instance.
     *
     * @param  \Mallabee\Queue\Drivers\Redis\Factory  $redis
     * @param  string|null  $connection
     * @return void
     */
    public function __construct(Redis $redis, $connection = null)
    {
        $this->redis = $redis;
        $this->connection = $connection;
    }

    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return QueueInterface
     */
    public function connect(array $config)
    {
        return new RedisQueue(
            $this->redis, $config['queue'],
            $config['connection'] ?? $this->connection,
            $config['retry_after'] ?? 60,
            $config['block_for'] ?? null
        );
    }
}
