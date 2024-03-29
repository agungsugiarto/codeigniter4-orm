<?php

namespace Fluent\Orm\Contracts;

use Fluent\Orm\Builder;
use Fluent\Orm\Model;

interface Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Fluent\Orm\Builder  $builder
     * @param  \Fluent\Orm\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model);
}
