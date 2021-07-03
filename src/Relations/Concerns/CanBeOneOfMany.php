<?php

namespace Fluent\Orm\Relations\Concerns;

use Closure;
use CodeIgniter\Database\BaseBuilder;
use Fluent\Orm\Builder;
use Fluent\Orm\Support\Str;
use InvalidArgumentException;
use Tightenco\Collect\Support\Arr;

trait CanBeOneOfMany
{
    /**
     * Determines whether the relationship is one-of-many.
     *
     * @var bool
     */
    protected $isOneOfMany = false;

    /**
     * The name of the relationship.
     *
     * @var string
     */
    protected $relationName;

    /**
     * Add constraints for inner join subselect for one of many relationships.
     *
     * @param  \Fluent\Orm\Builder  $query
     * @param  string|null  $column
     * @param  string|null  $aggregate
     * @return void
     */
    abstract public function addOneOfManySubQueryConstraints(Builder $query, $column = null, $aggregate = null);

    /**
     * Get the columns the determine the relationship groups.
     *
     * @return array|string
     */
    abstract public function getOneOfManySubQuerySelectColumns();

    /**
     * Add join query constraints for one of many relationships.
     *
     * @param  \CodeIgniter\Database\BaseBuilder  $join
     * @return void
     */
    abstract public function addOneOfManyJoinSubQueryConstraints(BaseBuilder $join);

    /**
     * Indicate that the relation is a single result of a larger one-to-many relationship.
     *
     * @param  string|array|null  $column
     * @param  string|Closure|null  $aggregate
     * @param  string|null  $relation
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function ofMany($column = 'id', $aggregate = 'MAX', $relation = null)
    {
        $this->isOneOfMany = true;

        $this->relationName = $relation ?: $this->getDefaultOneOfManyJoinAlias(
            $this->guessRelationship()
        );

        $keyName = $this->query->getModel()->getKeyName();

        $columns = is_string($columns = $column) ? [
            $column => $aggregate,
            $keyName => $aggregate,
        ] : $column;

        if (! array_key_exists($keyName, $columns)) {
            $columns[$keyName] = 'MAX';
        }

        if ($aggregate instanceof Closure) {
            $closure = $aggregate;
        }

        foreach ($columns as $column => $aggregate) {
            if (! in_array(strtolower($aggregate), ['min', 'max'])) {
                throw new InvalidArgumentException("Invalid aggregate [{$aggregate}] used within ofMany relation. Available aggregates: MIN, MAX");
            }

            $subQuery = $this->newOneOfManySubQuery(
                isset($previous) ? $previous['column'] : $this->getOneOfManySubQuerySelectColumns(),
                $column,
                $aggregate
            );

            if (isset($previous)) {
                $this->addOneOfManyJoinSubQuery($subQuery, $previous['subQuery'], $previous['column']);
            } elseif (isset($closure)) {
                $closure($subQuery);
            }

            if (array_key_last($columns) == $column) {
                $this->addOneOfManyJoinSubQuery($this->query, $subQuery, $column);
            }

            $previous = [
                'subQuery' => $subQuery,
                'column' => $column,
            ];
        }

        return $this;
    }

    /**
     * Indicate that the relation is the latest single result of a larger one-to-many relationship.
     *
     * @param  string|array|null  $column
     * @param  string|Closure|null  $aggregate
     * @param  string|null  $relation
     * @return $this
     */
    public function latestOfMany($column = 'id', $relation = null)
    {
        return $this->ofMany(collect(Arr::wrap($column))->mapWithKeys(function ($column) {
            return [$column => 'MAX'];
        })->all(), 'MAX', $relation ?: $this->guessRelationship());
    }

    /**
     * Indicate that the relation is the oldest single result of a larger one-to-many relationship.
     *
     * @param  string|array|null  $column
     * @param  string|Closure|null  $aggregate
     * @param  string|null  $relation
     * @return $this
     */
    public function oldestOfMany($column = 'id', $relation = null)
    {
        return $this->ofMany(collect(Arr::wrap($column))->mapWithKeys(function ($column) {
            return [$column => 'MIN'];
        })->all(), 'MIN', $relation ?: $this->guessRelationship());
    }

    /**
     * Get the default alias for the one of many inner join clause.
     *
     * @param  string  $relation
     * @return string
     */
    protected function getDefaultOneOfManyJoinAlias($relation)
    {
        return $relation == $this->query->getModel()->getTable()
            ? $relation . '_of_many'
            : $relation;
    }

    /**
     * Get a new query for the related model, grouping the query by the given column, often the foreign key of the relationship.
     *
     * @param  string|array  $groupBy
     * @param  string|null  $column
     * @param  string|null  $aggregate
     * @return \Fluent\Orm\Builder
     */
    protected function newOneOfManySubQuery($groupBy, $column = null, $aggregate = null)
    {
        $subQuery = $this->query->getModel()
            ->newQuery();

        foreach (Arr::wrap($groupBy) as $group) {
            $subQuery->groupBy($this->qualifyRelatedColumn($group));
        }

        if (! is_null($column)) {
            $subQuery->selectRaw($aggregate . '(' . $column . ') as ' . $column);
        }

        $this->addOneOfManySubQueryConstraints($subQuery, $groupBy, $column, $aggregate);

        return $subQuery;
    }

    /**
     * Add the join subquery to the given query on the given column and the relationship's foreign key.
     *
     * @param  \Fluent\Orm\Builder  $parent
     * @param  \Fluent\Orm\Builder  $subQuery
     * @param  string  $on
     * @return void
     */
    protected function addOneOfManyJoinSubQuery(Builder $parent, Builder $subQuery, $on)
    {
        $parent->joinSub($subQuery, $this->relationName, function ($join) use ($on) {
            $join->on($this->qualifySubSelectColumn($on), '=', $this->qualifyRelatedColumn($on));

            $this->addOneOfManyJoinSubQueryConstraints($join, $on);
        });
    }

    /**
     * Merge the relationship query joins to the given query builder.
     *
     * @param  \Fluent\Orm\Builder  $builder
     * @return void
     */
    protected function mergeOneOfManyJoinsTo(Builder $query)
    {
        $query->getQuery()->joins = $this->query->getQuery()->joins;

        $query->addBinding($this->query->getBindings(), 'join');
    }

    /**
     * Get the qualified column name for the one-of-many relationship using the subselect join query's alias.
     *
     * @param  string  $column
     * @return string
     */
    public function qualifySubSelectColumn($column)
    {
        return $this->getRelationName() . '.' . end(explode('.', $column));
    }

    /**
     * Qualify related column using the related table name if it is not already qualified.
     *
     * @param  string  $column
     * @return string
     */
    protected function qualifyRelatedColumn($column)
    {
        return Str::contains($column, '.') ? $column : $this->query->getModel()->getTable() . '.' . $column;
    }

    /**
     * Guess the "hasOne" relationship's name via backtrace.
     *
     * @return string
     */
    protected function guessRelationship()
    {
        return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'];
    }

    /**
     * Determine whether the relationship is a one-of-many relationship.
     *
     * @return bool
     */
    public function isOneOfMany()
    {
        return $this->isOneOfMany;
    }

    /**
     * Get the name of the relationship.
     *
     * @return string
     */
    public function getRelationName()
    {
        return $this->relationName;
    }
}
