<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/8/18
 * Time: 9:06 PM
 */

namespace Mallabee\Queue\Drivers\Beanstalkd;

use Mallabee\Queue\Core\QueueInterface;

class BeanstalkdQueueUtils extends \Mallabee\Queue\Core\QueueUtils
{
    /**
     * @return \Pheanstalk\Pheanstalk
     */
//    protected function getPheanstalk(): \Pheanstalk\Pheanstalk
//    {
//        /** @var BeanstalkdDriver $instance */
//        $instance = $this->instance;
//        $pheanstalk = $instance->getPheanstalk();
//
//        return $pheanstalk;
//    }

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
            $this->logger->info("Popped Job {$job->getJobId()}");
        }

        if (is_callable($onCleanFinished))
            $onCleanFinished();

        $this->logger->info("Clean queue ended..");
    }
}