<?php

namespace Fluent\Orm\Concerns;

use Tightenco\Collect\Support\Collection;

trait ExplainsQueries
{
    /**
     * Explains the query.
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    public function explain()
    {
        $sql = $this->toSql();

        $bindings = $this->query->getBinds();

        $explanation = $this->fromQuery('EXPLAIN ' . $sql, $bindings);

        return new Collection($explanation);
    }
}
