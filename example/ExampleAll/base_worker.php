<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/10/18
 * Time: 7:03 PM
 */
use Mallabee\Queue\CommonAdapters\LeagueEventDispatcher;
use Mallabee\Queue\Events\JobExceptionOccurred;
use Mallabee\Queue\Events\JobFailed;
use Mallabee\Queue\Events\JobProcessed;
use Mallabee\Queue\Events\JobProcessing;

require_once 'base_init_queue.php';

function getOptions(): array
{
    global $options;

    return $options;
}

function getOption(string $option)
{
    return array_key_exists($option, getOptions()) ? getOptions()[$option] : null;
}




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