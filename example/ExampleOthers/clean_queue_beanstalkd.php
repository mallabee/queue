<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/9/18
 * Time: 10:08 AM
 */

require_once '../../vendor/autoload.php';
require_once '../ExampleAll/base_init_queue.php';

$utils = new \Mallabee\Queue\Drivers\Beanstalkd\BeanstalkdQueueUtils($queue->getConnection());
try {
    $utils->cleanQueue();
}
catch (\Throwable $ex) {
    var_dump($ex->getMessage());
}

die('Finished Cleaning');
