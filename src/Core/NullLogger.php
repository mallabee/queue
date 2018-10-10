<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/8/18
 * Time: 8:02 PM
 */

namespace Mallabee\Queue\Core;


use Psr\Log\LoggerInterface;

class NullLogger implements LoggerInterface
{
    /**
     * @inheritDoc
     */
    public function emergency($message, array $context = array())
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function alert($message, array $context = array())
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function critical($message, array $context = array())
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = array())
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function warning($message, array $context = array())
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function notice($message, array $context = array())
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function info($message, array $context = array())
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function debug($message, array $context = array())
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = array())
    {
        //
    }
}