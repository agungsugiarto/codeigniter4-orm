<?php

namespace Fluent\Orm\Models\Relation;

use Fluent\Orm\Models\Model as BaseModel;
use Fluent\Orm\Models\Relation\Concerns\AsPivot;

/**
 * Class Pivot
 * @package Fluent\Orm\Models\Relation
 */
class Pivot extends BaseModel
{
    use AsPivot;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
