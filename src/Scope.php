<?php

namespace Fluent\Orm;

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
