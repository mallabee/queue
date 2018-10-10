<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/9/18
 * Time: 9:42 AM
 */

namespace Mallabee\Queue\Core;

class NullEventDispatcher implements EventDispatcherInterface
{
    /**
     * @inheritDoc
     */
    public function listen($events, $listener)
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function hasListeners($eventName)
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function subscribe($subscriber)
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function until($event, $payload = [])
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function dispatch($event, $payload = [], $halt = false)
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function push($event, $payload = [])
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function flush($event)
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function forget($event)
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function forgetPushed()
    {
        //
    }
}