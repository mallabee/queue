<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/10/18
 * Time: 7:03 PM
 */

use Mallabee\Queue\CommonAdapters\JmsJobSerializer;
use Mallabee\Queue\Core\Manager as Queue;

$serializer = new JmsJobSerializer(\JMS\Serializer\SerializerBuilder::create()->build());
$container = new \Mallabee\ExampleEasy\ExampleContainer($serializer);

// Create a Queue instance that the worker will be using
$queue = new Queue(!empty($addContainer) ? $container : null);

$instance = $queue->configure('beanstalkd', [
    'host' => 'localhost',
    'queue' => 'default',
]);