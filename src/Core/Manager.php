<?php
/**
 * @copyright   2016 Mallabee. All rights reserved
 * @author      Mallabee
 *
 * @link        https://www.mallabee.com
 */

namespace Mallabee\Queue\Core;

use Illuminate\Redis\RedisManager;
use \Mallabee\Queue\Drivers\Beanstalkd\BeanstalkdConnector;
use Mallabee\Queue\Drivers\Redis\RedisConnector;
use Mallabee\Queue\Drivers\Sqs\SqsConnector;
use Mallabee\Queue\Exceptions\NoConnectionFound;
use \Mallabee\Queue\Exceptions\UnsupportedDriver;
use Psr\Container\ContainerInterface;

class Manager
{
    /**
     * Name of the default connection
     */
    const DEFAULT_CONNECTION = 'default';

    /**
     * Container that will pass around
     *
     * @var null|ContainerInterface $container
     */
    protected $container;

    /**
     * Determine if the application is in maintenance mode.
     *
     * @var bool
     */
    protected $isDownForMaintenance = false;

    /**
     * The current globally used instance.
     *
     * @var Manager
     */
    protected static $instance;

    /**
     * The array of resolved queue connections.
     *
     * @var array
     */
    protected $connections = [];

    /**
     * The registered drivers
     *
     * @var array $drivers
     */
    protected static $drivers = [];

    /**
     * Manager constructor.
     *
     * @param null|ContainerInterface $container
     */
    public function __construct(?ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->registerDefaultDrivers();
    }

    /**
     * @return bool
     */
    public function isDownForMaintenance(): bool
    {
        return $this->isDownForMaintenance;
    }

    /**
     * @param bool $isDownForMaintenance
     * @return Manager
     */
    public function setIsDownForMaintenance(bool $isDownForMaintenance): Manager
    {
        $this->isDownForMaintenance = $isDownForMaintenance;
        return $this;
    }

    /**
     * Make this manager instance available globally.
     *
     * @return void
     */
    public function setAsGlobal()
    {
        static::$instance = $this;
    }

    /**
     * Register the default drivers for the queues
     */
    private function registerDefaultDrivers()
    {
        try {
            $this->registerDriver('beanstalkd', new BeanstalkdConnector());
            //$this->registerDriver('beanstalkd', 'Beanstalkd', '\Drivers\Beanstalkd', 'Drivers/Beanstalkd');
        } catch (\Exception $ex) {
        }

        try {
            $this->registerDriver('sqs', new SqsConnector());
        } catch (\Exception $ex) {
        }

//        try {
//            $this->registerDriver('redis', new RedisConnector(new RedisManager(null, 'predis', [])));
//        } catch (\Exception $ex) {
//        }
    }

    /**
     * @param string $driverName
     * @param array $config
     * @param string $connectionName
     * @return \Mallabee\Queue\Core\QueueInterface
     */
    public function configure(string $driverName, array $config, string $connectionName = 'default'): QueueInterface
    {
        if (!array_key_exists($driverName, self::$drivers))
            throw new UnsupportedDriver("Driver {$driverName} is not registered");

        /** @var QueueConnectorInterface $connector */
        $connector = self::$drivers[$driverName];

        /** @var Queue|QueueInterface $connection */
        $connection = $connector->connect($config);
        $connection->setContainer((!empty($this->container)) ? $this->container : null);
        $this->connections[$connectionName] = $connection;

        return $connection;
    }

    /**
     * @param string $driverName
     * @param QueueConnectorInterface $connector
     */
    public function registerDriver(string $driverName, QueueConnectorInterface $connector)
    {
        self::$drivers[$driverName] = $connector;
    }

    /**
     * Resolve a queue connection instance.
     *
     * @param  string  $name
     * @return QueueInterface
     */
    public function connection($name = self::DEFAULT_CONNECTION): QueueInterface
    {
        if (is_null($name))
            $name = self::DEFAULT_CONNECTION;

        if (!array_key_exists($name, $this->connections))
            throw new NoConnectionFound("Could not find connection with name of ${name}");

        return $this->connections[$name];
    }

    /**
     * Get a registered connection instance.
     *
     * @param  string  $name
     * @return QueueInterface
     */
    public function getConnection($name = self::DEFAULT_CONNECTION): QueueInterface
    {
        return $this->connection($name);
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string $job
     * @param  mixed $data
     * @param  string $queue
     * @param  string $connection
     * @return mixed
     */
    public static function push($job, $data = '', $queue = null, $connection = null)
    {
        return static::$instance->connection($connection)->push($job, $data, $queue);
    }

    /**
     * Push a new an array of jobs onto the queue.
     *
     * @param  array $jobs
     * @param  mixed $data
     * @param  string $queue
     * @param  string $connection
     * @return mixed
     */
    public static function bulk($jobs, $data = '', $queue = null, $connection = null)
    {
        return static::$instance->connection($connection)->bulk($jobs, $data, $queue);
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  \DateTimeInterface|\DateInterval|int $delay
     * @param  string $job
     * @param  mixed $data
     * @param  string $queue
     * @param  string $connection
     * @return mixed
     */
    public static function later($delay, $job, $data = '', $queue = null, $connection = null)
    {
        return static::$instance->connection($connection)->later($delay, $job, $data, $queue);
    }
}