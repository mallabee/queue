<?php
/**
 * Created by PhpStorm.
 * User: dinbracha
 * Date: 10/8/18
 * Time: 10:35 PM
 */

namespace Mallabee\ExampleEasy;

use Mallabee\Queue\Core\JobSerializerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ExampleContainer implements ContainerInterface
{
    /** @var array $di */
    protected $di = [];

    /**
     * ExampleContainer constructor.
     *
     * @param JobSerializerInterface $serializer
     */
    public function __construct(JobSerializerInterface $serializer)
    {
        $this->di['logger'] = new ExampleLogger();
        $this->di['job_serializer'] = $serializer;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        return (array_key_exists($id, $this->di)) ? $this->di[$id] : null;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        return array_key_exists($id, $this->di);
    }
}