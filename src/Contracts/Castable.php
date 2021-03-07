<?php

namespace Fluent\Orm\Contracts;

interface Castable
{
    /**
     * Get the name of the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return string
     * @return string|\Fluent\Orm\Contracts\CastsAttributes|\Fluent\Orm\Contracts\CastsInboundAttributes
     */
    public static function castUsing(array $arguments);
}
