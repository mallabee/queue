<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/8/18
 * Time: 10:35 PM
 */

require_once '../../vendor/autoload.php';

use Mallabee\Queue\Core\Manager as Queue;

$queue = new Queue;

$instance = $queue->configure('beanstalkd', [
    'host' => 'localhost',
    'queue' => 'default',
]);

// If you like - set last configured instance as global
$queue->setAsGlobal();


// Create Jobs
$jobId = $queue->push(\Mallabee\Example\PrintMessageJob::class, ['message' => "I am job 1"]);
echo "Job {$jobId} was pushed" . PHP_EOL;

$jobId = Queue::push(\Mallabee\Example\PrintMessageJob::class, ['message' => "I am job 2"]);
echo "Job {$jobId} was pushed" . PHP_EOL;