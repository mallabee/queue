<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/9/18
 * Time: 10:08 AM
 */

require_once '../../vendor/autoload.php';

use Mallabee\Queue\CommonAdapters\JmsJobSerializer;
use Mallabee\Queue\CommonAdapters\LeagueEventDispatcher;
use Mallabee\Queue\Core\Manager as Queue;
use Mallabee\Queue\Core\Worker;
use Mallabee\Queue\Core\WorkerOptions;
use Mallabee\Queue\Events\JobExceptionOccurred;
use Mallabee\Queue\Events\JobFailed;
use Mallabee\Queue\Events\JobProcessed;
use Mallabee\Queue\Events\JobProcessing;
use Mallabee\Queue\Events\WorkerStopping;


// Defaults
$options = [
    'delay' => 0,
    'memory' => 128,
    'timeout' => 60,
    'sleep' => 3,
    'tries' => 0,
    'force' => false,
    'stop-when-empty' => true
];

function getOptions(): array
{
    global $options;

    return $options;
}

function getOption(string $option)
{
    return array_key_exists($option, getOptions()) ? getOptions()[$option] : null;
}



// Create a Queue instance that the worker will be using
$serializer = new JmsJobSerializer(\JMS\Serializer\SerializerBuilder::create()->build());
$container = new \Mallabee\ExampleEasy\ExampleContainer($serializer);
$queue = new Queue($container);

$instance = $queue->configure('beanstalkd', [
    'host' => 'localhost',
    'queue' => 'default',
]);




// Prepare the worker events
$eventsDispatcher = new LeagueEventDispatcher;
$eventsDispatcher->listen(JobProcessing::class, function ($leagueEvent, $queueEvent, $payload) {
    /** @var JobProcessing $queueEvent */
    $jobId = $queueEvent->job->getJobId();
    echo "Job {$jobId} starting" . PHP_EOL;
});
$eventsDispatcher->listen(JobProcessed::class, function ($leagueEvent, $queueEvent, $payload) {
    /** @var JobProcessed $queueEvent */
    $jobId = $queueEvent->job->getJobId();
    echo "Job {$jobId} success" . PHP_EOL;

    // Delete job after finishing processing it
    $queueEvent->job->delete();
});
$eventsDispatcher->listen(JobFailed::class, function ($leagueEvent, $queueEvent, $payload) {
    /** @var JobFailed $queueEvent */
    $jobId = $queueEvent->job->getJobId();
    echo "Job {$jobId} failed" . PHP_EOL;

    // $this->logFailedJob($queueEvent);
});
$eventsDispatcher->listen(JobExceptionOccurred::class, function ($leagueEvent, $queueEvent, $payload) {
    /** @var JobExceptionOccurred $queueEvent */
    $jobId = $queueEvent->job->getJobId();
    echo "Job {$jobId} exception: {$queueEvent->exception->getMessage()}" . PHP_EOL;

    // Delete job due to exception - e.g.: we removed the Job handler so "Class 'SendEmail' not found"
    $queueEvent->job->delete();

    // $this->logFailedJob($queueEvent);
});



// Initiate a worker
$worker = new Worker($queue, $eventsDispatcher);


// Run the worker until there are no more jobs
$options['stop-when-empty'] = true;
$workerOptions = new WorkerOptions(
    getOption('delay'), getOption('memory'),
    getOption('timeout'), getOption('sleep'),
    getOption('tries'), getOption('force'),
    getOption('stop-when-empty')
);
$eventsDispatcher->listen(WorkerStopping::class, function ($leagueEvent, $queueEvent, $payload) {
    /** @var WorkerStopping $queueEvent */

    echo 'Sleeping 3..' . PHP_EOL;
    sleep (3);
    echo 'Finished all works..' . PHP_EOL;

    // $this->logFailedJob($queueEvent);
});
try {
    $worker->daemon(Queue::DEFAULT_CONNECTION, 'default', $workerOptions);
}
catch (\Throwable $ex) {
    var_dump($ex->getMessage());
    var_dump($ex->getFile());
    var_dump($ex->getLine());
}