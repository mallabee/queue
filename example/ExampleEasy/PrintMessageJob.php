<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/8/18
 * Time: 10:35 PM
 */

namespace Mallabee\ExampleEasy;

use Mallabee\Queue\Core\InteractsWithQueue;
use Mallabee\Queue\Core\JobUtils;

class PrintMessageJob
{
    use InteractsWithQueue, JobUtils;

    /** @var string $message */
    public $message;

    /**
     * @param $job
     * @param $payload
     *
     * @throws \ReflectionException
     */
    public function fire($job, $payload)
    {
        $this->populate($payload);

        /*var_dump($job);
        var_dump($payload);
        var_dump($this->message);
        die('ss');*/

        echo $this->message . PHP_EOL;
    }
}