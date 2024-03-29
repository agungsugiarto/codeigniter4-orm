<?php

namespace Fluent\Orm\Relations;

use CodeIgniter\Database\BaseBuilder;
use Fluent\Orm\Builder;
use Fluent\Orm\Collection;
use Fluent\Orm\Contracts\SupportsPartialRelations;
use Fluent\Orm\Model;
use Fluent\Orm\Relations\Concerns\CanBeOneOfMany;
use Fluent\Orm\Relations\Concerns\ComparesRelatedModels;
use Fluent\Orm\Relations\Concerns\SupportsDefaultModels;

class MorphOne extends MorphOneOrMany implements SupportsPartialRelations
{
    use CanBeOneOfMany;
    use ComparesRelatedModels;
    use SupportsDefaultModels;

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        if (is_null($this->getParentKey())) {
            return $this->getDefaultFor($this->parent);
        }

        return $this->query->first() ?: $this->getDefaultFor($this->parent);
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  array  $models
     * @param  string  $relation
     * @return array
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->getDefaultFor($model));
        }

        return $models;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array  $models
     * @param  \Fluent\Orm\Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        return $this->matchOne($models, $results, $relation);
    }

    /**
     * Get the relationship query.
     *
     * @param  \Fluent\Orm\Builder  $query
     * @param  \Fluent\Orm\Builder  $parentQuery
     * @param  array|mixed  $columns
     * @return \Fluent\Orm\Builder
     */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        if ($this->isOneOfMany()) {
            $this->mergeOneOfManyJoinsTo($query);
        }

        return parent::getRelationExistenceQuery($query, $parentQuery, $columns);
    }

    /**
     * Add constraints for inner join subselect for one of many relationships.
     *
     * @param  \Fluent\Orm\Builder  $query
     * @param  string|null  $column
     * @param  string|null  $aggregate
     * @return void
     */
    public function addOneOfManySubQueryConstraints(Builder $query, $column = null, $aggregate = null)
    {
        $query->addSelect($this->foreignKey, $this->morphType);
    }

    /**
     * Get the columns that should be selected by the one of many subquery.
     *
     * @return array|string
     */
    public function getOneOfManySubQuerySelectColumns()
    {
        return [$this->foreignKey, $this->morphType];
    }

    /**
     * Add join query constraints for one of many relationships.
     *
     * @param  \CodeIgniter\Database\BaseBuilder $query
     * @return void
     */
    public function addOneOfManyJoinSubQueryConstraints(BaseBuilder $query)
    {
        $query
            ->join($query->getTable(), "{$this->qualifySubSelectColumn($this->morphType)} = {$this->qualifyRelatedColumn($this->morphType)}")
            ->join($query->getTable(), "{$this->qualifySubSelectColumn($this->foreignKey)} = {$this->qualifyRelatedColumn($this->foreignKey)}");
    }

    /**
     * Make a new related instance for the given model.
     *
     * @param  \Fluent\Orm\Model  $parent
     * @return \Fluent\Orm\Model
     */
    public function newRelatedInstanceFor(Model $parent)
    {
        return $this->related->newInstance()
                    ->setAttribute($this->getForeignKeyName(), $parent->{$this->localKey})
                    ->setAttribute($this->getMorphType(), $this->morphClass);
    }

    /**
     * Get the value of the model's foreign key.
     *
     * @param  \Fluent\Orm\Model  $model
     * @return mixed
     */
    protected function getRelatedKeyFrom(Model $model)
    {
        return $model->getAttribute($this->getForeignKeyName());
    }
}
