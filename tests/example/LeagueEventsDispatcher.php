<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/8/18
 * Time: 10:35 PM
 */

namespace Mallabee\Example;

use League\Event\Emitter;
use Mallabee\Example\Events\JobExceptionOccurredEvent;
use Mallabee\Example\Events\JobFailedEvent;
use Mallabee\Example\Events\JobProcessedEvent;
use Mallabee\Example\Events\JobProcessingEvent;
use Mallabee\Example\Events\LoopingEvent;
use Mallabee\Example\Events\WorkerStoppingEvent;
use Mallabee\Queue\Core\EventDispatcherInterface;
use Mallabee\Queue\Events\JobExceptionOccurred;
use Mallabee\Queue\Events\JobFailed;
use Mallabee\Queue\Events\JobProcessed;
use Mallabee\Queue\Events\JobProcessing;
use Mallabee\Queue\Events\Looping;
use Mallabee\Queue\Events\WorkerStopping;

class LeagueEventsDispatcher implements EventDispatcherInterface
{
    public $emitter;

    /**
     * MyEventsDispatcher constructor.
     */
    public function __construct()
    {
        $this->emitter = new Emitter;
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param  string|array $events
     * @param  mixed $listener
     * @return void
     */
    public function listen($events, $listener)
    {
        $this->emitter->addListener($events, $listener);
    }

    /**
     * Determine if a given event has listeners.
     *
     * @param  string $eventName
     * @return bool
     */
    public function hasListeners($eventName)
    {
        // TODO: Implement hasListeners() method.
    }

    /**
     * Register an event subscriber with the dispatcher.
     *
     * @param  object|string $subscriber
     * @return void
     */
    public function subscribe($subscriber)
    {
        // TODO: Implement subscribe() method.
    }

    /**
     * Dispatch an event until the first non-null response is returned.
     *
     * @param  string|object $event
     * @param  mixed $payload
     * @return array|null
     */
    public function until($event, $payload = [])
    {
        // TODO: Implement until() method.
    }

    /**
     * Dispatch an event and call the listeners.
     *
     * @param  string|object $event
     * @param  mixed $payload
     * @param  bool $halt
     * @return array|null
     */
    public function dispatch($event, $payload = [], $halt = false)
    {
        try {
//            $leagueEvent = null;
//            switch (true)
//            {
//                case $event instanceof JobExceptionOccurred:
//                    $leagueEvent = new JobExceptionOccurredEvent($event->connectionName, $event->job, $event->exception);
//                    break;
//                case $event instanceof JobFailed:
//                    $leagueEvent = new JobFailedEvent($event->connectionName, $event->job, $event->exception);
//                    break;
//                case $event instanceof JobProcessed:
//                    $leagueEvent = new JobProcessedEvent($event->connectionName, $event->job);
//                    break;
//                case $event instanceof JobProcessing:
//                    $leagueEvent = new JobProcessingEvent($event->connectionName, $event->job);
//                    break;
//                case $event instanceof Looping:
//                    $leagueEvent = new LoopingEvent($event->connectionName, $event->queue);
//                    break;
//                case $event instanceof WorkerStopping:
//                    $leagueEvent = new WorkerStoppingEvent($event->status);
//                    break;
//            }
//            $leagueEvent->payload = $payload;
//var_dump($event);die();
            //$dispatchedEvent = $this->emitter->emit($event);
            /*[
                'event' => $event,
                'payload' => $payload,
                'halt' => $halt
            ]*/


            $dispatchedEvent = $this->emitter->emit(get_class($event), $event, $payload);
            //var_dump($dispatchedEvent);
        }
        catch (\Exception $ex) {
            var_dump(get_class($ex));
            var_dump($ex->getMessage());
        }
        //die('gg');
        return $dispatchedEvent;
    }

    /**
     * Register an event and payload to be fired later.
     *
     * @param  string $event
     * @param  array $payload
     * @return void
     */
    public function push($event, $payload = [])
    {
        // TODO: Implement push() method.
    }

    /**
     * Flush a set of pushed events.
     *
     * @param  string $event
     * @return void
     */
    public function flush($event)
    {
        // TODO: Implement flush() method.
    }

    /**
     * Remove a set of listeners from the dispatcher.
     *
     * @param  string $event
     * @return void
     */
    public function forget($event)
    {
        // TODO: Implement forget() method.
    }

    /**
     * Forget all of the queued listeners.
     *
     * @return void
     */
    public function forgetPushed()
    {
        // TODO: Implement forgetPushed() method.
    }
}