<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/9/18
 * Time: 10:08 AM
 */

require_once '../../vendor/autoload.php';
require_once '../ExampleAll/base_worker.php';

use Mallabee\Queue\Core\Worker;

// Initiate a worker
$worker = new Worker($queue, $eventsDispatcher);

// Peek on next job
try {
    $job = $worker->getNextJob($instance, 'default');

    if (!is_null($job)) {
        var_dump($job->getRawBody());
    }
}
catch (\Throwable $ex) {
    var_dump($ex->getMessage());
}