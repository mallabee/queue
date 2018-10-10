<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/10/18
 * Time: 7:03 PM
 */
require_once '../../vendor/autoload.php';

use Mallabee\Queue\Core\Manager as Queue;
use Mallabee\Queue\Core\Worker;
use Mallabee\Queue\Core\WorkerOptions;
use Mallabee\Queue\Events\WorkerStopping;

require_once './base_worker.php';

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
$eventsDispatcher->listen(WorkerStopping::class, function ($leagueEvent, $queueEvent, $payload) {
    /** @var WorkerStopping $queueEvent */

    echo 'Sleeping 3..' . PHP_EOL;
    sleep (3);
    echo 'Finished all works..' . PHP_EOL;

    // $this->logFailedJob($queueEvent);
});

// Initiate a worker
$worker = new Worker($queue, $eventsDispatcher);

// Run the worker daemonized - less preferred
$workerOptions = new WorkerOptions(
    getOption('delay'), getOption('memory'),
    getOption('timeout'), getOption('sleep'),
    getOption('tries'), getOption('force'),
    getOption('stop-when-empty')
);

try {
    $worker->daemon(Queue::DEFAULT_CONNECTION, 'default', $workerOptions);
}
catch (\Throwable $ex) {
    var_dump($ex->getMessage());
}