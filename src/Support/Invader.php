<?php

namespace Fluent\Orm\Support;

use ReflectionClass;

/**
 * This class offers an invade function that will allow you to read/write private properties of an object.
 * It will also allow you to set, get and call private methods.
 *
 * @see https://github.com/spatie/invade/blob/main/src/Invader.php
 */
class Invader
{
    /** @var object */
    public $object;

    /** @var ReflectionClass */
    public $reflected;

    public function __construct(object $object)
    {
        $this->object = $object;
        $this->reflected = new ReflectionClass($object);
    }

    /**
     * {@inheritdoc}
     */
    public function __get($name)
    {
        $property = $this->reflected->getProperty($name);

        $property->setAccessible(true);

        return $property->getValue($this->object);
    }

    /**
     * {@inheritdoc}
     */
    public function __set($name, $value)
    {
        $property = $this->reflected->getProperty($name);

        $property->setAccessible(true);

        $property->setValue($this->object, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function __call($name, $arguments)
    {
        $method = $this->reflected->getMethod($name);

        $method->setAccessible(true);

        return $method->invoke($this->object, ...$arguments);
    }
}