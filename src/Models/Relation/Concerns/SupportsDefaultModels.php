<?php

namespace Fluent\Models\Relation\Concerns;

use Fluent\Models\Model;

trait SupportsDefaultModels
{
    /**
     * Indicates if a default model instance should be used.
     *
     * Alternatively, may be a Closure or array.
     *
     * @var \Closure|array|bool
     */
    protected $withDefault;

    /**
     * Make a new related instance for the given model.
     *
     * @param  Model  $parent
     * @return Model
     */
    abstract protected function newRelatedInstanceFor(Model $parent);

    /**
     * Return a new model instance in case the relationship does not exist.
     *
     * @param  \Closure|array|bool  $callback
     * @return $this
     */
    public function withDefault($callback = true)
    {
        $this->withDefault = $callback;

        return $this;
    }

    /**
     * Get the default value for this relation.
     *
     * @param  Model  $parent
     * @return Model|null
     */
    protected function getDefaultFor(Model $parent)
    {
        if (! $this->withDefault) {
            return;
        }

        $instance = $this->newRelatedInstanceFor($parent);

        if (is_callable($this->withDefault)) {
            return call_user_func($this->withDefault, $instance, $parent) ?: $instance;
        }

        if (is_array($this->withDefault)) {
            $instance->forceFill($this->withDefault);
        }

        return $instance;
    }
}