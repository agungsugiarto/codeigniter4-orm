<?php

namespace Fluent\Orm\Tests\Pagination;

use Fluent\Orm\Pagination\Cursor;
use Fluent\Orm\Pagination\CursorPaginator;
use PHPUnit\Framework\TestCase;

class CursorPaginatorTest extends TestCase
{
    public function testReturnsRelevantContextInformation()
    {
        $p = new CursorPaginator($array = [['id' => 1], ['id' => 2], ['id' => 3]], 2, null, [
            'parameters' => ['id'],
        ]);

        $this->assertTrue($p->hasPages());
        $this->assertTrue($p->hasMorePages());
        $this->assertEquals([['id' => 1], ['id' => 2]], $p->items());

        $pageInfo = [
            'data' => [['id' => 1], ['id' => 2]],
            'path' => '/',
            'per_page' => 2,
            'next_page_url' => '/?cursor='.$this->getCursor(['id' => 2]),
            'prev_page_url' => null,
        ];

        $this->assertEquals($pageInfo, $p->toArray());
    }

    public function testPaginatorRemovesTrailingSlashes()
    {
        $p = new CursorPaginator($array = [['id' => 4], ['id' => 5], ['id' => 6]], 2, null,
            ['path' => 'http://website.com/test/', 'parameters' => ['id']]);

        $this->assertSame('http://website.com/test?cursor='.$this->getCursor(['id' => 5]), $p->nextPageUrl());
    }

    public function testPaginatorGeneratesUrlsWithoutTrailingSlash()
    {
        $p = new CursorPaginator($array = [['id' => 4], ['id' => 5], ['id' => 6]], 2, null,
            ['path' => 'http://website.com/test', 'parameters' => ['id']]);

        $this->assertSame('http://website.com/test?cursor='.$this->getCursor(['id' => 5]), $p->nextPageUrl());
    }

    public function testItRetrievesThePaginatorOptions()
    {
        $p = new CursorPaginator($array = [['id' => 4], ['id' => 5], ['id' => 6]], 2, null,
            $options = ['path' => 'http://website.com/test', 'parameters' => ['id']]);

        $this->assertSame($p->getOptions(), $options);
    }

    public function testPaginatorReturnsPath()
    {
        $p = new CursorPaginator($array = [['id' => 4], ['id' => 5], ['id' => 6]], 2, null,
            $options = ['path' => 'http://website.com/test', 'parameters' => ['id']]);

        $this->assertSame($p->path(), 'http://website.com/test');
    }

    public function testCanTransformPaginatorItems()
    {
        $p = new CursorPaginator($array = [['id' => 4], ['id' => 5], ['id' => 6]], 2, null,
            $options = ['path' => 'http://website.com/test', 'parameters' => ['id']]);

        $p->through(function ($item) {
            $item['id'] = $item['id'] + 2;

            return $item;
        });

        $this->assertInstanceOf(CursorPaginator::class, $p);
        $this->assertSame([['id' => 6], ['id' => 7]], $p->items());
    }

    protected function getCursor($params, $isNext = true)
    {
        return (new Cursor($params, $isNext))->encode();
    }
}