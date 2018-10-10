<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/10/18
 * Time: 7:03 PM
 */
use Mallabee\Queue\Core\Manager as Queue;


// Create a Queue instance that the worker will be using
$queue = new Queue;

$instance = $queue->configure('beanstalkd', [
    'host' => 'localhost',
    'queue' => 'default',
]);