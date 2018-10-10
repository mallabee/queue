<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/9/18
 * Time: 10:08 AM
 */

require_once '../../vendor/autoload.php';

use Mallabee\Example\LeagueEventsDispatcher;
use Mallabee\Queue\Core\Manager as Queue;
use Mallabee\Queue\Core\Worker;
use Mallabee\Queue\Core\WorkerOptions;
use Mallabee\Queue\Events\JobExceptionOccurred;
use Mallabee\Queue\Events\JobFailed;
use Mallabee\Queue\Events\JobProcessed;
use Mallabee\Queue\Events\JobProcessing;


// Defaults
$options = [
    'delay' => 0,
    'memory' => 128,
    'timeout' => 60,
    'sleep' => 3,
    'tries' => 0,
    'force' => false,
    'stop-when-empty' => false
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
$queue = new Queue;

$instance = $queue->configure('beanstalkd', [
    'host' => 'localhost',
    'queue' => 'default',
]);




// Prepare the worker options
$workerOptions = new WorkerOptions(
    getOption('delay'), getOption('memory'),
    getOption('timeout'), getOption('sleep'),
    getOption('tries'), getOption('force'),
    getOption('stop-when-empty')
);

$eventsDispatcher = new LeagueEventsDispatcher;
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

// Run the worker daemonized - less preferred
try {
    $worker->daemon(Queue::DEFAULT_CONNECTION, 'default', $workerOptions);
}
catch (\Exception $ex) {
    var_dump($ex->getMessage());
}

// Run the worker until there are no more jobs
try {
    $worker->daemon(Queue::DEFAULT_CONNECTION, 'default', $workerOptions);
    sleep (3);
}
catch (\Exception $ex) {
    var_dump($ex->getMessage());
}

// Run one job
try {
    $worker->runNextJob(Queue::DEFAULT_CONNECTION, 'default', $workerOptions);
}
catch (\Exception $ex) {
    var_dump($ex->getMessage());
}

// Peek on next job - if you like
/*$job = $worker->getNextJob($instance, 'default');
var_dump($job->getRawBody());*/
