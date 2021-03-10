<?php

namespace Fluent\Orm\Casts;

use ArrayObject as BaseArrayObject;
use JsonSerializable;
use Tightenco\Collect\Contracts\Support\Arrayable;

class ArrayObject extends BaseArrayObject implements Arrayable, JsonSerializable
{
    /**
     * Get a collection containing the underlying array.
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    public function collect()
    {
        return collect($this->getArrayCopy());
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getArrayCopy();
    }

    /**
     * Get the array that should be JSON serialized.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getArrayCopy();
    }
}
