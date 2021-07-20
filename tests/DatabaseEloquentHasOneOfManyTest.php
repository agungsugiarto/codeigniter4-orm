<?php

namespace Fluent\Orm\Tests;

use CodeIgniter\Database\Config;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Fluent\Orm\Model as Eloquent;
use Fluent\Orm\SoftDeletes;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @group one-of-many
 */
class DatabaseEloquentHasOneOfManyTest extends TestCase
{
    /**
     * Setup the database schema.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->createSchema();
    }

    protected function createSchema()
    {
        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('users', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('logins', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'state' => ['type' => 'varchar', 'constraint' => 255],
            'type' => ['type' => 'varchar', 'constraint' => 255],
        ])
        ->addPrimaryKey('id')
        ->createTable('states', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'published_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('prices', true);
    }

    protected function tearDown(): void
    {
        $this->schema()->dropTable('users', true);
        $this->schema()->dropTable('logins', true);
        $this->schema()->dropTable('states', true);
        $this->schema()->dropTable('prices', true);
    }

    // public function testItGuessesRelationName()
    // {
    //     $user = HasOneOfManyTestUser::make();
    //     $this->assertSame('latest_login', $user->latest_login()->getRelationName());
    // }

    // public function testItGuessesRelationNameAndAddsOfManyWhenTableNameIsRelationName()
    // {
    //     $model = HasOneOfManyTestModel::make();
    //     $this->assertSame('logins_of_many', $model->logins()->getRelationName());
    // }

    // public function testRelationNameCanBeSet()
    // {
    //     $user = HasOneOfManyTestUser::create();

    //     // Using "ofMany"
    //     $relation = $user->latest_login()->ofMany('id', 'max', 'foo');
    //     $this->assertSame('foo', $relation->getRelationName());

    //     // Using "latestOfMAny"
    //     $relation = $user->latest_login()->latestOfMAny('id', 'bar');
    //     $this->assertSame('bar', $relation->getRelationName());

    //     // Using "oldestOfMAny"
    //     $relation = $user->latest_login()->oldestOfMAny('id', 'baz');
    //     $this->assertSame('baz', $relation->getRelationName());
    // }

    // public function testEagerLoadingAppliesConstraintsToInnerJoinSubQuery()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $relation = $user->latest_login();
    //     $relation->addEagerConstraints([$user]);
    //     $this->assertSame('select MAX(id) as id, "logins"."user_id" from "logins" where "logins"."user_id" = ? and "logins"."user_id" is not null and "logins"."user_id" in (1) group by "logins"."user_id"', $relation->getOneOfManySubQuery()->toSql());
    // }

    // public function testQualifyingSubSelectColumn()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $this->assertSame('latest_login.id', $user->latest_login()->qualifySubSelectColumn('id'));
    // }

    public function testItFailsWhenUsingInvalidAggregate()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid aggregate [count] used within ofMany relation. Available aggregates: MIN, MAX');
        $user = HasOneOfManyTestUser::make();
        $user->latest_login_with_invalid_aggregate();
    }

    // public function testItGetsCorrectResults()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $previousLogin = $user->logins()->create();
    //     $latestLogin = $user->logins()->create();

    //     $result = $user->latest_login()->getResults();
    //     $this->assertNotNull($result);
    //     $this->assertSame($latestLogin->id, $result->id);
    // }

    // public function testItGetsCorrectResultsUsingShortcutMethod()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $previousLogin = $user->logins()->create();
    //     $latestLogin = $user->logins()->create();

    //     $result = $user->latest_login_with_shortcut()->getResults();
    //     $this->assertNotNull($result);
    //     $this->assertSame($latestLogin->id, $result->id);
    // }

    // public function testItGetsCorrectResultsUsingShortcutReceivingMultipleColumnsMethod()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $user->prices()->create([
    //         'published_at' => '2021-05-01 00:00:00',
    //     ]);
    //     $price = $user->prices()->create([
    //         'published_at' => '2021-05-01 00:00:00',
    //     ]);

    //     $result = $user->price_with_shortcut()->getResults();
    //     $this->assertNotNull($result);
    //     $this->assertSame($price->id, $result->id);
    // }

    // public function testKeyIsAddedToAggregatesWhenMissing()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $user->prices()->create([
    //         'published_at' => '2021-05-01 00:00:00',
    //     ]);
    //     $price = $user->prices()->create([
    //         'published_at' => '2021-05-01 00:00:00',
    //     ]);

    //     $result = $user->price_without_key_in_aggregates()->getResults();
    //     $this->assertNotNull($result);
    //     $this->assertSame($price->id, $result->id);
    // }

    // public function testItGetsWithConstraintsCorrectResults()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $previousLogin = $user->logins()->create();
    //     $user->logins()->create();

    //     $result = $user->latest_login()->whereKey($previousLogin->getKey())->getResults();
    //     $this->assertNull($result);
    // }

    // public function testItEagerLoadsCorrectModels()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $user->logins()->create();
    //     $latestLogin = $user->logins()->create();

    //     $user = HasOneOfManyTestUser::with('latest_login')->first();

    //     $this->assertTrue($user->relationLoaded('latest_login'));
    //     $this->assertSame($latestLogin->id, $user->latest_login->id);
    // }

    // public function testHasNested()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $previousLogin = $user->logins()->create();
    //     $latestLogin = $user->logins()->create();

    //     $found = HasOneOfManyTestUser::whereHas('latest_login', function ($query) use ($latestLogin) {
    //         $query->where('logins.id', $latestLogin->id);
    //     })->exists();
    //     $this->assertTrue($found);

    //     $found = HasOneOfManyTestUser::whereHas('latest_login', function ($query) use ($previousLogin) {
    //         $query->where('logins.id', $previousLogin->id);
    //     })->exists();
    //     $this->assertFalse($found);
    // }

    // public function testHasCount()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $user->logins()->create();
    //     $user->logins()->create();

    //     $user = HasOneOfManyTestUser::withCount('latest_login')->first();
    //     $this->assertEquals(1, $user->latest_login_count);
    // }

    // public function testExists()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $previousLogin = $user->logins()->create();
    //     $latestLogin = $user->logins()->create();

    //     $this->assertFalse($user->latest_login()->whereKey($previousLogin->getKey())->exists());
    //     $this->assertTrue($user->latest_login()->whereKey($latestLogin->getKey())->exists());
    // }

    // public function testIsMethod()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $login1 = $user->latest_login()->create();
    //     $login2 = $user->latest_login()->create();

    //     $this->assertFalse($user->latest_login()->is($login1));
    //     $this->assertTrue($user->latest_login()->is($login2));
    // }

    // public function testIsNotMethod()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $login1 = $user->latest_login()->create();
    //     $login2 = $user->latest_login()->create();

    //     $this->assertTrue($user->latest_login()->isNot($login1));
    //     $this->assertFalse($user->latest_login()->isNot($login2));
    // }

    // public function testGet()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $previousLogin = $user->logins()->create();
    //     $latestLogin = $user->logins()->create();

    //     $latestLogins = $user->latest_login()->get();
    //     $this->assertCount(1, $latestLogins);
    //     $this->assertSame($latestLogin->id, $latestLogins->first()->id);

    //     $latestLogins = $user->latest_login()->whereKey($previousLogin->getKey())->get();
    //     $this->assertCount(0, $latestLogins);
    // }

    // public function testCount()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $user->logins()->create();
    //     $user->logins()->create();

    //     $this->assertSame(1, $user->latest_login()->count());
    // }

    // public function testAggregate()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $firstLogin = $user->logins()->create();
    //     $user->logins()->create();

    //     $user = HasOneOfManyTestUser::first();
    //     $this->assertSame($firstLogin->id, $user->first_login->id);
    // }

    // public function testJoinConstraints()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $user->states()->create([
    //         'type' => 'foo',
    //         'state' => 'draft',
    //     ]);
    //     $currentForState = $user->states()->create([
    //         'type' => 'foo',
    //         'state' => 'active',
    //     ]);
    //     $user->states()->create([
    //         'type' => 'bar',
    //         'state' => 'baz',
    //     ]);

    //     $user = HasOneOfManyTestUser::first();
    //     $this->assertSame($currentForState->id, $user->foo_state->id);
    // }

    // public function testMultipleAggregates()
    // {
    //     $user = HasOneOfManyTestUser::create();

    //     $user->prices()->create([
    //         'published_at' => '2021-05-01 00:00:00',
    //     ]);
    //     $price = $user->prices()->create([
    //         'published_at' => '2021-05-01 00:00:00',
    //     ]);

    //     $user = HasOneOfManyTestUser::first();
    //     $this->assertSame($price->id, $user->price->id);
    // }

    // public function testEagerLoadingWithMultipleAggregates()
    // {
    //     $user1 = HasOneOfManyTestUser::create();
    //     $user2 = HasOneOfManyTestUser::create();

    //     $user1->prices()->create([
    //         'published_at' => '2021-05-01 00:00:00',
    //     ]);
    //     $user1Price = $user1->prices()->create([
    //         'published_at' => '2021-05-01 00:00:00',
    //     ]);
    //     $user1->prices()->create([
    //         'published_at' => '2021-04-01 00:00:00',
    //     ]);

    //     $user2Price = $user2->prices()->create([
    //         'published_at' => '2021-05-01 00:00:00',
    //     ]);
    //     $user2->prices()->create([
    //         'published_at' => '2021-04-01 00:00:00',
    //     ]);

    //     $users = HasOneOfManyTestUser::with('price')->get();

    //     $this->assertNotNull($users[0]->price);
    //     $this->assertSame($user1Price->id, $users[0]->price->id);

    //     $this->assertNotNull($users[1]->price);
    //     $this->assertSame($user2Price->id, $users[1]->price->id);
    // }

    // public function testWithExists()
    // {
    //     $user = HasOneOfManyTestUser::create();

    //     $user = HasOneOfManyTestUser::withExists('latest_login')->first();
    //     $this->assertFalse($user->latest_login_exists);

    //     $user->logins()->create();
    //     $user = HasOneOfManyTestUser::withExists('latest_login')->first();
    //     $this->assertTrue($user->latest_login_exists);
    // }

    // public function testWithExistsWithConstraintsInJoinSubSelect()
    // {
    //     $user = HasOneOfManyTestUser::create();

    //     $user = HasOneOfManyTestUser::withExists('foo_state')->first();

    //     $this->assertFalse($user->foo_state_exists);

    //     $user->states()->create([
    //         'type' => 'foo',
    //         'state' => 'bar',
    //     ]);
    //     $user = HasOneOfManyTestUser::withExists('foo_state')->first();
    //     $this->assertTrue($user->foo_state_exists);
    // }

    // public function testWithSoftDeletes()
    // {
    //     $user = HasOneOfManyTestUser::create();
    //     $user->logins()->create();
    //     $user->latest_login_with_soft_deletes;
    //     $this->assertNotNull($user->latest_login_with_soft_deletes);
    // }

    /**
     * Get a schema builder instance.
     *
     * @return \CodeIgniter\Database\Forge
     */
    protected function schema()
    {
        return Config::forge();
    }
}

/**
 * Eloquent Models...
 */
class HasOneOfManyTestUser extends Eloquent
{
    protected $table = 'users';
    protected $guarded = [];
    public $timestamps = false;

    public function logins()
    {
        return $this->hasMany(HasOneOfManyTestLogin::class, 'user_id');
    }

    public function latest_login()
    {
        return $this->hasOne(HasOneOfManyTestLogin::class, 'user_id')->ofMany();
    }

    public function latest_login_with_soft_deletes()
    {
        return $this->hasOne(HasOneOfManyTestLoginWithSoftDeletes::class, 'user_id')->ofMany();
    }

    public function latest_login_with_shortcut()
    {
        return $this->hasOne(HasOneOfManyTestLogin::class, 'user_id')->latestOfMany();
    }

    public function latest_login_with_invalid_aggregate()
    {
        return $this->hasOne(HasOneOfManyTestLogin::class, 'user_id')->ofMany('id', 'count');
    }

    public function first_login()
    {
        return $this->hasOne(HasOneOfManyTestLogin::class, 'user_id')->ofMany('id', 'min');
    }

    public function states()
    {
        return $this->hasMany(HasOneOfManyTestState::class, 'user_id');
    }

    public function foo_state()
    {
        return $this->hasOne(HasOneOfManyTestState::class, 'user_id')->ofMany(
            ['id' => 'max'],
            function ($q) {
                $q->where('type', 'foo');
            }
        );
    }

    public function prices()
    {
        return $this->hasMany(HasOneOfManyTestPrice::class, 'user_id');
    }

    public function price()
    {
        return $this->hasOne(HasOneOfManyTestPrice::class, 'user_id')->ofMany([
            'published_at' => 'max',
            'id' => 'max',
        ], function ($q) {
            $q->where('published_at', '<', now());
        });
    }

    public function price_without_key_in_aggregates()
    {
        return $this->hasOne(HasOneOfManyTestPrice::class, 'user_id')->ofMany(['published_at' => 'MAX']);
    }

    public function price_with_shortcut()
    {
        return $this->hasOne(HasOneOfManyTestPrice::class, 'user_id')->latestOfMany(['published_at', 'id']);
    }
}

class HasOneOfManyTestModel extends Eloquent
{
    public function logins()
    {
        return $this->hasOne(HasOneOfManyTestLogin::class)->ofMany();
    }
}

class HasOneOfManyTestLogin extends Eloquent
{
    protected $table = 'logins';
    protected $guarded = [];
    public $timestamps = false;
}

class HasOneOfManyTestLoginWithSoftDeletes extends Eloquent
{
    use SoftDeletes;

    protected $table = 'logins';
    protected $guarded = [];
    public $timestamps = false;
}

class HasOneOfManyTestState extends Eloquent
{
    protected $table = 'states';
    protected $guarded = [];
    public $timestamps = false;
    protected $fillable = ['type', 'state'];
}

class HasOneOfManyTestPrice extends Eloquent
{
    protected $table = 'prices';
    protected $guarded = [];
    public $timestamps = false;
    protected $fillable = ['published_at'];
    protected $casts = ['published_at' => 'datetime'];
}