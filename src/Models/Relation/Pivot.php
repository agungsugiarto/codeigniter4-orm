<?php

namespace Fluent\Models\Relation;

use Fluent\Models\Model as BaseModel;
use Fluent\Models\Relation\Concerns\AsPivot;

/**
 * Class Pivot
 * @package Fluent\Models\Relation
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
