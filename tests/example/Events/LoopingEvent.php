<?php

namespace Mallabee\Example\Events;

use Mallabee\Queue\Events\Looping;

class LoopingEvent extends Looping implements \League\Event\EventInterface
{
    /**
     * Worker payload
     *
     * @var array
     */
    public $payload;

    /**
     * Set the Emitter.
     *
     * @param EmitterInterface $emitter
     *
     * @return $this
     */
    public function setEmitter(EmitterInterface $emitter)
    {
        // TODO: Implement setEmitter() method.
    }

    /**
     * Get the Emitter.
     *
     * @return EmitterInterface
     */
    public function getEmitter()
    {
        // TODO: Implement getEmitter() method.
    }

    /**
     * Stop event propagation.
     *
     * @return $this
     */
    public function stopPropagation()
    {
        // TODO: Implement stopPropagation() method.
    }

    /**
     * Check whether propagation was stopped.
     *
     * @return bool
     */
    public function isPropagationStopped()
    {
        // TODO: Implement isPropagationStopped() method.
    }

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
        //return str_replace("Event", "", $reflect->getShortName());
    }
}