<?php

namespace Mallabee\Example\Events;

use Mallabee\Queue\Events\JobProcessing;

class JobProcessingEvent extends JobProcessing implements \League\Event\EventInterface
{
    /**
     * Worker payload
     *
     * @var array
     */
    public $payload;

    /**
     * Get the event name.
     *
     * @return string
     *
     * @throws \ReflectionException
     */
    public function getName()
    {
        $reflect = new \ReflectionClass($this);
        return $reflect->getShortName();
    }
}