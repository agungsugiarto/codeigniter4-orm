<?php

namespace Fluent\Models\Relation;

use Fluent\Models\BaseModel as Model;
use Fluent\Models\Relation\Concerns\AsPivot;

class Pivot extends Model
{
    use AsPivot;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}