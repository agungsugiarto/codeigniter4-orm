<?php

use Fluent\Orm\Support\Invader;

if (! function_exists('last')) {
    /**
     * Get the last element from an array.
     *
     * @param  array  $array
     * @return mixed
     */
    function last($array)
    {
        return end($array);
    }
}

if (! function_exists('factory')) {
    /**
     * Create a model factory builder for a given class and amount.
     *
     * @param  string  $class
     * @param  int  $amount
     * @return \Fluent\Orm\FactoryBuilder
     */
    function factory($class, $amount = null)
    {
        /** @var \Fluent\Orm\Factory $factory */
        $factory = service('factory');

        if (isset($amount) && is_int($amount)) {
            return $factory->of($class)->times($amount);
        }

        return $factory->of($class);
    }
}

if (! function_exists('invade')) {
    /**
     * This class offers an invade function that will allow you to read/write private properties of an object.
     * It will also allow you to set, get and call private methods.
     *
     * @param object $object
     * @return Invader
     *
     * @see https://github.com/spatie/invade/blob/main/src/Invader.php
     */
    function invade(object $object)
    {
        return new Invader($object);
    }
}
