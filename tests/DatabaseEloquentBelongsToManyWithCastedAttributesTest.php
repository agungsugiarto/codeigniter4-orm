<?php

namespace Fluent\Orm\Tests;

use Fluent\Orm\Builder;
use Fluent\Orm\Collection;
use Fluent\Orm\Model;
use Fluent\Orm\Relations\BelongsToMany;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class DatabaseEloquentBelongsToManyWithCastedAttributesTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testModelsAreProperlyMatchedToParents()
    {
        $relation = $this->getRelation();
        $model1 = m::mock(Model::class);
        $model1->shouldReceive('getAttribute')->with('parent_key')->andReturn(1);
        $model1->shouldReceive('getAttribute')->with('foo')->passthru();
        $model1->shouldReceive('hasGetMutator')->andReturn(false);
        $model1->shouldReceive('getCasts')->andReturn([]);
        $model1->shouldReceive('getRelationValue', 'relationLoaded', 'setRelation', 'isRelation')->passthru();

        $model2 = m::mock(Model::class);
        $model2->shouldReceive('getAttribute')->with('parent_key')->andReturn(2);
        $model2->shouldReceive('getAttribute')->with('foo')->passthru();
        $model2->shouldReceive('hasGetMutator')->andReturn(false);
        $model2->shouldReceive('getCasts')->andReturn([]);
        $model2->shouldReceive('getRelationValue', 'relationLoaded', 'setRelation', 'isRelation')->passthru();

        $result1 = (object) [
            'pivot' => (object) [
                'foreign_key' => new class
                {
                    public function __toString()
                    {
                        return '1';
                    }
                },
            ],
        ];

        $models = $relation->match([$model1, $model2], Collection::wrap($result1), 'foo');
        self::assertNull($models[1]->foo);
        self::assertEquals(1, $models[0]->foo->count());
        self::assertContains($result1, $models[0]->foo);
    }

    protected function getRelation()
    {
        $builder = m::mock(Builder::class);
        $related = m::mock(Model::class);
        $related->shouldReceive('newCollection')->passthru();
        $builder->shouldReceive('getModel')->andReturn($related);
        $related->shouldReceive('qualifyColumn');
        $builder->shouldReceive('join', 'where');

        return new BelongsToMany(
            $builder,
            new EloquentBelongsToManyModelStub,
            'relation',
            'foreign_key',
            'id',
            'parent_key',
            'related_key'
        );
    }
}

class EloquentBelongsToManyModelStub extends Model
{
    public $foreign_key = 'foreign.value';
}