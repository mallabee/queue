<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/8/18
 * Time: 7:05 PM
 */

namespace Mallabee\Queue\Core;

use Psr\Log\LoggerInterface;

/**
 * Utils that hold specific implementations for the driver will be in here
 *
 * Class QueueUtils
 * @package Core
 */
class QueueUtils
{
    /** @var LoggerInterface $logger */
    protected $logger;

    /** @var Queue $instance */
    protected $instance;

    /**
     * QueueUtils constructor.
     *
     * @param QueueInterface $queue
     * @param null|LoggerInterface $logger
     */
    public function __construct(QueueInterface $queue, ?LoggerInterface $logger = null)
    {
        $this->instance = $queue;
        $this->logger = !empty($logger) ? $logger : new NullLogger();
    }

    /**
     * Pull jobs from queues and clean the queue
     *
     * @param callable|null $onCleanFinished
     *
     * @throws \Exception Throws exception on job fails
     */
    abstract public function cleanQueue($onCleanFinished = null);
}