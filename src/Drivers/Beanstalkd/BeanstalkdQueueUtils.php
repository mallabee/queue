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
     * Default allowed maximum pool of jobs
     *
     * @var int
     */
    const DEFAULT_MAX_POOL = 1000;

    /**
     * @return \Pheanstalk\Pheanstalk
     */
    protected function getPheanstalk(): \Pheanstalk\Pheanstalk
    {
        /** @var BeanstalkdDriver $instance */
        $instance = $this->instance;
        $pheanstalk = $instance->getPheanstalk();

        return $pheanstalk;
    }

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

        $pheanstalk = $this->getPheanstalk();

        $pheanstalk->useTube($queueConfig->queueName);
        $pheanstalk->watch($queueConfig->queueName);
        while ($job = $pheanstalk->reserve(0)) {
            $this->logger->info("Deleting job");

            $pheanstalk->delete($job);
        }

        if (is_callable($onCleanFinished))
            $onCleanFinished();

        $this->logger->info("Clean queue ended..");
    }

    /**
     * Run a queue
     *
     * @param callable|null $onJobFinished
     * @param callable|null $onError
     *
     * @return \Generator
     *
     * @throws \Exception
     */
    public function runQueue($onJobFinished = null, $onError = null)
    {
        $pheanstalk = $this->getPheanstalk();

        // Continuously loop to endlessly monitor beanstalk queue
        while (true) {
            // Checks beanstalk connection
            if (!$pheanstalk->getConnection()->isServiceListening()) {
                $this->logger->error("error connecting to beanstalk, sleeping for 5 seconds...");

                // Sleep for 5 seconds
                sleep(5);

                // Skip to next iteration of loop
                continue;
            }

            try {
                $this->logger->info('listening to queue');
                // Get job from queue, if none wait for a job to be available
                $job = $pheanstalk
                    ->watch($queueConfig->queueName)
                    ->ignore('default')
                    ->reserve();

                yield $job->getData();

                /** @var \CoreBundle\Core\BaseWorker $workerInstance */
                $workerInstance = $this->workerInstance;

                //if ($workerInstance) {
                $this->logger->info("Starting work - with work data:");
                var_dump($workerInstance->getWorkPayload());
                $workerInstance->run($workerInstance->getWorkPayload());

                // Remove the job from the queue
                $pheanstalk->delete($job);

                $this->logger->info("Work ended");

                if (!is_null($onJobFinished))
                    $onJobFinished();
            } catch (\Exception $e) {
                $errorCode = $e->getCode();
                $errorMessage = $e->getMessage();
                if ($errorCode != Errors::GENERAL_ERROR && Errors::isSupported($errorCode))
                    $errorMessage = Errors::getValue($errorCode);

                $this->logger->error("Error starting worker due to error code {$errorCode} - {$errorMessage}");
                $this->logger->error("Possible timeout for queue - catch 2, exception type: " . get_class($e) . ", message: " . $errorMessage . ", code: " . $errorCode);

                if ($e instanceof \Symfony\Component\Process\Exception\ProcessFailedException) {
                    $this->logger->error("Dying.. Error starting worker due to error code {$errorCode} - {$errorMessage}");
                    throw new \Exception("Dying.. Error starting worker due to error code {$errorCode} - {$errorMessage}");
                }

                if (is_callable($onError))
                    $onError($e, $pheanstalk, $job);
            }

            $this->workerInstance = null;
        }
    }

    /**
     * Get jobs from the queue
     * Note: this deletes jobs immediately because it doesn't return a generator
     *
     * @param boolean $peek Should we peek the queue without deleting the jobs or not
     * @param int $limit
     *
     * @return array
     */
    public function getJobs($peek = false, $limit = 5)
    {
        $pheanstalk = $this->getPheanstalk();

        $currentPool = 0;
        $jobs = [];

        // Continuously loop until we get the amount of jobs requested
        while (true) {
            // Checks beanstalk connection
            if (!$pheanstalk->getConnection()->isServiceListening()) {
                $this->logger->error("error connecting to beanstalk, sleeping for 5 seconds...");

                // Sleep for 5 seconds
                sleep(5);

                // Skip to next iteration of loop
                continue;
            }

            try {
                $this->logger->info('listening to queue');
                // Get job from queue, if no jobs for X seconds - return current jobs
                $job = $pheanstalk
                    ->watch($queueConfig->queueName)
                    ->ignore('default')
                    ->reserve(1);

                if (!empty($job)) {
                    $jobs[] = $job->getData();

                    if (!$peek) {
                        // Remove the job from the queue
                        $pheanstalk->delete($job);
                    }

                    // Limit the max amount of jobs to 1000
                    if ($currentPool >= $limit || $currentPool >= self::DEFAULT_MAX_POOL)
                        break;

                    $currentPool++;
                } else {
                    // No more jobs waiting - use just the jobs that were successfully pooled
                    break;
                }
            } catch (\Exception $e) {
                $errorCode = $e->getCode();
                $errorMessage = $e->getMessage();
                if ($errorCode != Errors::GENERAL_ERROR && Errors::isSupported($errorCode))
                    $errorMessage = Errors::getValue($errorCode);

                $this->logger->error("Error getting worker due to error code {$errorCode} - {$errorMessage}");

                // Don't die - just continue
                //				if ($e instanceof \Symfony\Component\Process\Exception\ProcessFailedException) {
                //					die("Dying.. Error starting worker due to error code {$errorCode} - {$errorMessage}");
                //				}
            }
        }

        return $jobs;
    }
}