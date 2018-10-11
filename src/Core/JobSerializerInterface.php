<?php

namespace Mallabee\Queue\Core;

interface JobSerializerInterface
{
    /**
     * Get the job serializer/encoder.
     *
     * @param mixed $payload
     *
     * @return string
     */
    public function encode($payload);

    /**
     * Get the job deserializer/decoder.
     *
     * @param string $jobClass
     * @param null|string $payload
     *
     * @return array|object
     */
    public function decode(string $jobClass, ?string $payload);
}
