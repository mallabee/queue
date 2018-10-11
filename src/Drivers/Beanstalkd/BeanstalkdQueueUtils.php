<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/8/18
 * Time: 9:06 PM
 */

namespace Mallabee\Queue\Drivers\Beanstalkd;

class BeanstalkdQueueUtils extends \Mallabee\Queue\Core\QueueUtils
{
    /**
     * Pull jobs from queues and clean the queue
     *
     * @param callable|null $onCleanFinished
     *
     * @throws \Exception Throws exception on job fails
     */
    public function cleanQueue($onCleanFinished = null)
    {
        $this->logger->info("Cleaning queue...");

        while (!is_null($job = $this->instance->pop())) {
            $job->delete();
            $this->logger->info("Popped job {$job->getJobId()}");
        }

        if (is_callable($onCleanFinished))
            $onCleanFinished();

        $this->logger->info("Clean queue ended..");
    }
}