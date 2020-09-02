<?php

namespace Fluent\Models\Relation;

use Exception;
use Fluent\Models\Model;
use Fluent\Models\Relation\Concerns\SupportsDefaultModels;

/**
 * Class HasOne
 * @package Fluent\Models\Relation
 */
class HasOne extends HasOneOrMany
{
    use SupportsDefaultModels;

    /**
     * Get the results of the relationship.
     *
     * @return Model|mixed|null
     * @throws Exception
     */
    public function getResults()
    {
        if (is_null($this->getParentKey())) {
            return $this->getDefaultFor($this->parent);
        }

        return $this->related->first() ?: $this->getDefaultFor($this->parent);
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  array   $models
     * @param  string  $relation
     * @return array
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setLoadedRelation($relation, $this->getDefaultFor($model));
        }

        return $models;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array  $models
     * @param  array  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, array $results, $relation)
    {
        return $this->matchOne($models, $results, $relation);
    }

    /**
     * Make a new related instance for the given model.
     *
     * @param  Model  $parent
     * @return Model
     */
    public function newRelatedInstanceFor(Model $parent)
    {
        return $this->related->newModelQuery()->setAttribute(
            $this->getForeignKeyName(),
            $parent->{$this->localKey}
        );
    }
}
