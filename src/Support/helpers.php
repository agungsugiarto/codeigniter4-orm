<?php

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
