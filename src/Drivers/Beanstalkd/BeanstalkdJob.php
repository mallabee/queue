<?php

namespace Mallabee\Queue\Drivers\Beanstalkd;

use Mallabee\Queue\Core\Job;
use Pheanstalk\Pheanstalk;
use Psr\Container\ContainerInterface;
use Pheanstalk\Job as PheanstalkJob;

/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/8/18
 * Time: 5:05 PM
 */

class BeanstalkdJob extends Job implements \Mallabee\Queue\Core\JobInterface
{
    /**
     * The Pheanstalk instance.
     *
     * @var \Pheanstalk\Pheanstalk
     */
    protected $pheanstalk;

    /**
     * The Pheanstalk job instance.
     *
     * @var \Pheanstalk\Job
     */
    protected $job;

    /**
     * Create a new job instance.
     *
     * @param  null|\Psr\Container\ContainerInterface  $container
     * @param  \Pheanstalk\Pheanstalk  $pheanstalk
     * @param  \Pheanstalk\Job  $job
     * @param  string  $connectionName
     * @param  string  $queue
     * @return void
     */
    public function __construct(?ContainerInterface $container, Pheanstalk $pheanstalk, PheanstalkJob $job, $connectionName, $queue)
    {
        $this->job = $job;
        $this->queue = $queue;
        $this->container = $container;
        $this->pheanstalk = $pheanstalk;
        $this->connectionName = $connectionName;
    }

    /**
     * Release the job back into the queue.
     *
     * @param  int   $delay
     * @return void
     */
    public function release($delay = 0)
    {
        parent::release($delay);

        $priority = Pheanstalk::DEFAULT_PRIORITY;

        $this->pheanstalk->release($this->job, $priority, $delay);
    }

    /**
     * Bury the job in the queue.
     *
     * @return void
     */
    public function bury()
    {
        parent::release();

        $this->pheanstalk->bury($this->job);
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();

        $this->pheanstalk->delete($this->job);
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        $stats = $this->pheanstalk->statsJob($this->job);

        return (int) $stats->reserves;
    }

    /**
     * Get the job identifier.
     *
     * @return int
     */
    public function getJobId()
    {
        return $this->job->getId();
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->job->getData();
    }

    /**
     * Get the underlying Pheanstalk instance.
     *
     * @return \Pheanstalk\Pheanstalk
     */
    public function getPheanstalk()
    {
        return $this->pheanstalk;
    }

    /**
     * Get the underlying Pheanstalk job.
     *
     * @return \Pheanstalk\Job
     */
    public function getPheanstalkJob()
    {
        return $this->job;
    }
}