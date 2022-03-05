<?php

namespace Fluent\Orm;

use CodeIgniter\Database\Config;
use Fluent\Orm\Model as Eloquent;
use PHPUnit\Framework\TestCase;

class DatabaseEloquentMorphOneOfManyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createSchema();
    }

    /**
     * Setup the database schema.
     *
     * @return void
     */
    public function createSchema()
    {
        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('products', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'stateful_type' => ['type' => 'varchar', 'constraint' => 255],
            'stateful_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'state' => ['type' => 'varchar', 'constraint' => 255],
            'type' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->addKey(['stateful_type', 'stateful_id'])
        ->createTable('states', true);
    }

    /**
     * Tear down the database schema.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->schema()->dropTable('products', true);
        $this->schema()->dropTable('states', true);
    }

    // public function testEagerLoadingAppliesConstraintsToInnerJoinSubQuery()
    // {
    //     $product = new MorphOneOfManyTestProduct();
    //     $relation = $product->current_state();
    //     $relation->addEagerConstraints([$product]);
    //     $this->assertSame('select MAX("states"."id") as "id_aggregate", "states"."stateful_id", "states"."stateful_type" from "states" where "states"."stateful_type" = ? and "states"."stateful_id" = ? and "states"."stateful_id" is not null and "states"."stateful_id" in (1) and "states"."stateful_type" = ? group by "states"."stateful_id", "states"."stateful_type"', $relation->getOneOfManySubQuery()->toSql());
    // }

    // public function testReceivingModel()
    // {
    //     $product = MorphOneOfManyTestProduct::create();
    //     $product->states()->create([
    //         'state' => 'draft',
    //     ]);
    //     $product->states()->create([
    //         'state' => 'active',
    //     ]);

    //     $this->assertNotNull($product->current_state);
    //     $this->assertSame('active', $product->current_state->state);
    // }

    // public function testMorphType()
    // {
    //     $product = MorphOneOfManyTestProduct::create();
    //     $product->states()->create([
    //         'state' => 'draft',
    //     ]);
    //     $product->states()->create([
    //         'state' => 'active',
    //     ]);
    //     $state = $product->states()->make([
    //         'state' => 'foo',
    //     ]);
    //     $state->stateful_type = 'bar';
    //     $state->save();

    //     $this->assertNotNull($product->current_state);
    //     $this->assertSame('active', $product->current_state->state);
    // }

    // public function testExists()
    // {
    //     $product = MorphOneOfManyTestProduct::create();
    //     $previousState = $product->states()->create([
    //         'state' => 'draft',
    //     ]);
    //     $currentState = $product->states()->create([
    //         'state' => 'active',
    //     ]);

    //     $exists = MorphOneOfManyTestProduct::whereHas('current_state', function ($q) use ($previousState) {
    //         $q->whereKey($previousState->getKey());
    //     })->exists();
    //     $this->assertFalse($exists);

    //     $exists = MorphOneOfManyTestProduct::whereHas('current_state', function ($q) use ($currentState) {
    //         $q->whereKey($currentState->getKey());
    //     })->exists();
    //     $this->assertTrue($exists);
    // }

    // public function testWithExists()
    // {
    //     $product = MorphOneOfManyTestProduct::create();

    //     $product = MorphOneOfManyTestProduct::withExists('current_state')->first();
    //     $this->assertFalse($product->current_state_exists);

    //     $product->states()->create([
    //         'state' => 'draft',
    //     ]);
    //     $product = MorphOneOfManyTestProduct::withExists('current_state')->first();
    //     $this->assertTrue($product->current_state_exists);
    // }

    // public function testWithExistsWithConstraintsInJoinSubSelect()
    // {
    //     $product = MorphOneOfManyTestProduct::create();

    //     $product = MorphOneOfManyTestProduct::withExists('current_foo_state')->first();
    //     $this->assertFalse($product->current_foo_state_exists);

    //     $product->states()->create([
    //         'state' => 'draft',
    //         'type' => 'foo',
    //     ]);
    //     $product = MorphOneOfManyTestProduct::withExists('current_foo_state')->first();
    //     $this->assertTrue($product->current_foo_state_exists);
    // }

    protected function schema()
    {
        return Config::forge();
    }
}

/**
 * Eloquent Models...
 */
class MorphOneOfManyTestProduct extends Eloquent
{
    protected $table = 'products';
    protected $guarded = [];
    public $timestamps = false;

    public function states()
    {
        return $this->morphMany(MorphOneOfManyTestState::class, 'stateful');
    }

    public function current_state()
    {
        return $this->morphOne(MorphOneOfManyTestState::class, 'stateful')->ofMany();
    }

    public function current_foo_state()
    {
        return $this->morphOne(MorphOneOfManyTestState::class, 'stateful')->ofMany(
            ['id' => 'max'],
            function ($q) {
                $q->where('type', 'foo');
            }
        );
    }
}

class MorphOneOfManyTestState extends Eloquent
{
    protected $table = 'states';
    protected $guarded = [];
    public $timestamps = false;
    protected $fillable = ['state', 'type'];
}