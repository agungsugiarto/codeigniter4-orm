<?php

namespace Fluent\Orm\Tests\Pagination;

use Fluent\Orm\Collection;
use Fluent\Orm\Pagination\AbstractCursorPaginator;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class CursorPaginatorLoadMorphCountTest extends TestCase
{
    public function testCollectionLoadMorphCountCanChainOnThePaginator()
    {
        $relations = [
            'App\\User' => 'photos',
            'App\\Company' => ['employees', 'calendars'],
        ];

        $items = m::mock(Collection::class);
        $items->shouldReceive('loadMorphCount')->once()->with('parentable', $relations);

        $p = (new class extends AbstractCursorPaginator
        {
            //
        })->setCollection($items);

        $this->assertSame($p, $p->loadMorphCount('parentable', $relations));
    }
}