<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/8/18
 * Time: 10:35 PM
 */

namespace Mallabee\ExampleEasy;

use Mallabee\Queue\Core\InteractsWithQueue;
use Mallabee\Queue\Core\JobInterface;
use Mallabee\Queue\Core\JobUtils;
use Mallabee\Queue\Drivers\Beanstalkd\BeanstalkdJob;
use Psr\Log\LoggerInterface;

class PrintMessageJob
{
    use InteractsWithQueue, JobUtils;

    /** @var string $message */
    public $message;

    /**
     * @param JobInterface $job
     * @param $payload
     *
     * @throws \ReflectionException
     */
    public function fire($job, $payload)
    {
        /** @var BeanstalkdJob $job */

        $this->populate($payload);

        if (!empty($job->getContainer())) {
            /** @var LoggerInterface $logger */
            $logger = $job->getContainer()->get('logger');
            if (!empty($logger))
                $logger->info('Logging using container');
        }

        echo $this->message . PHP_EOL;
    }
}