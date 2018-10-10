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
     * @param Queue $queueConnection
     * @param null|LoggerInterface $logger
     */
    public function __construct(Queue $queueConnection, ?LoggerInterface $logger = null)
    {
        $this->instance = $queueConnection;
        $this->logger = !empty($logger) ? $logger : new NullLogger();
    }
}