<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/8/18
 * Time: 10:35 PM
 */

namespace Mallabee\Queue\CommonAdapters;

use Mallabee\Queue\Core\JobSerializerInterface;

class JmsJobSerializer implements JobSerializerInterface
{
    /** @var \JMS\Serializer\SerializerInterface $serializer */
    protected $serializer;

    /**
     * JmsJobSerializer constructor.
     *
     * @param \JMS\Serializer\SerializerInterface $serializer
     */
    public function __construct(\JMS\Serializer\SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function encode($payload)
    {
        return $this->serializer->serialize($payload, 'json');
    }

    /**
     * @inheritDoc
     */
    public function decode(string $jobClass, ?string $payload)
    {
        return $this->serializer->deserialize($payload, $jobClass, 'json');
    }
}