<?php

namespace Fluent\Orm\Tests;

use CodeIgniter\Database\Config;
use Fluent\Orm\Model as Eloquent;
use Fluent\Orm\Exceptions\ModelNotFoundException;
use Fluent\Orm\SoftDeletes;
use PHPUnit\Framework\TestCase;

class DatabaseEloquentHasOneThroughIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
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
            'email' => ['type' => 'varchar', 'constraint' => 255, 'unique' => true],
            'position_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'unique' => true, 'null' => true],
            'position_short' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('users', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'varchar', 'constraint' => 255, 'unique' => true],
            'title' => ['type' => 'varchar', 'constraint' => 255],
            'body' => ['type' => 'varchar', 'constraint' => 255],
            'email' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('contracts', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'varchar', 'constraint' => 255],
            'shortname' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('positions', true);
    }

    /**
     * Tear down the database schema.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->schema()->dropTable('users');
        $this->schema()->dropTable('contracts');
        $this->schema()->dropTable('positions');
    }

    public function testItLoadsAHasOneThroughRelationWithCustomKeys()
    {
        $this->seedData();
        $contract = HasOneThroughTestPosition::first()->contract;

        $this->assertSame('A title', $contract->title);
    }

    public function testItLoadsADefaultHasOneThroughRelation()
    {
        $this->migrateDefault();
        $this->seedDefaultData();

        $contract = HasOneThroughDefaultTestPosition::first()->contract;
        $this->assertSame('A title', $contract->title);
        $this->assertArrayNotHasKey('email', $contract->getAttributes());

        $this->resetDefault();
    }

    public function testItLoadsARelationWithCustomIntermediateAndLocalKey()
    {
        $this->seedData();
        $contract = HasOneThroughIntermediateTestPosition::first()->contract;

        $this->assertSame('A title', $contract->title);
    }

    public function testEagerLoadingARelationWithCustomIntermediateAndLocalKey()
    {
        $this->seedData();
        $contract = HasOneThroughIntermediateTestPosition::with('contract')->first()->contract;

        $this->assertSame('A title', $contract->title);
    }

    public function testWhereHasOnARelationWithCustomIntermediateAndLocalKey()
    {
        $this->seedData();
        $position = HasOneThroughIntermediateTestPosition::whereHas('contract', function ($query) {
            $query->where('title', 'A title');
        })->get();

        $this->assertCount(1, $position);
    }

    public function testFirstOrFailThrowsAnException()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('No query results for model [Fluent\Orm\Tests\HasOneThroughTestContract].');

        HasOneThroughTestPosition::create(['id' => 1, 'name' => 'President', 'shortname' => 'ps'])
            ->user()->create(['id' => 1, 'email' => 'taylorotwell@gmail.com', 'position_short' => 'ps']);

        HasOneThroughTestPosition::first()->contract()->firstOrFail();
    }

    public function testFindOrFailThrowsAnException()
    {
        $this->expectException(ModelNotFoundException::class);

        HasOneThroughTestPosition::create(['id' => 1, 'name' => 'President', 'shortname' => 'ps'])
            ->user()->create(['id' => 1, 'email' => 'taylorotwell@gmail.com', 'position_short' => 'ps']);

        HasOneThroughTestPosition::first()->contract()->findOrFail(1);
    }

    public function testFirstRetrievesFirstRecord()
    {
        $this->seedData();
        $contract = HasOneThroughTestPosition::first()->contract()->first();

        $this->assertNotNull($contract);
        $this->assertSame('A title', $contract->title);
    }

    public function testAllColumnsAreRetrievedByDefault()
    {
        $this->seedData();
        $contract = HasOneThroughTestPosition::first()->contract()->first();
        $this->assertEquals([
            'id',
            'user_id',
            'title',
            'body',
            'email',
            'created_at',
            'updated_at',
            'laravel_through_key',
        ], array_keys($contract->getAttributes()));
    }

    public function testOnlyProperColumnsAreSelectedIfProvided()
    {
        $this->seedData();
        $contract = HasOneThroughTestPosition::first()->contract()->first(['title', 'body']);

        $this->assertEquals([
            'title',
            'body',
            'laravel_through_key',
        ], array_keys($contract->getAttributes()));
    }

    public function testChunkReturnsCorrectModels()
    {
        $this->seedData();
        $this->seedDataExtended();
        $position = HasOneThroughTestPosition::find(1);

        $position->contract()->chunk(10, function ($contractsChunk) {
            $contract = $contractsChunk->first();
            $this->assertEquals([
                'id',
                'user_id',
                'title',
                'body',
                'email',
                'created_at',
                'updated_at',
                'laravel_through_key', ], array_keys($contract->getAttributes()));
        });
    }

    public function testCursorReturnsCorrectModels()
    {
        $this->seedData();
        $this->seedDataExtended();
        $position = HasOneThroughTestPosition::find(1);

        $contracts = $position->contract()->cursor();

        foreach ($contracts as $contract) {
            $this->assertEquals([
                'id',
                'user_id',
                'title',
                'body',
                'email',
                'created_at',
                'updated_at',
                'laravel_through_key', ], array_keys($contract->getAttributes()));
        }
    }

    public function testEachReturnsCorrectModels()
    {
        $this->seedData();
        $this->seedDataExtended();
        $position = HasOneThroughTestPosition::find(1);

        $position->contract()->each(function ($contract) {
            $this->assertEquals([
                'id',
                'user_id',
                'title',
                'body',
                'email',
                'created_at',
                'updated_at',
                'laravel_through_key', ], array_keys($contract->getAttributes()));
        });
    }

    public function testLazyReturnsCorrectModels()
    {
        $this->seedData();
        $this->seedDataExtended();
        $position = HasOneThroughTestPosition::find(1);

        $position->contract()->lazy()->each(function ($contract) {
            $this->assertEquals([
                'id',
                'user_id',
                'title',
                'body',
                'email',
                'created_at',
                'updated_at',
                'laravel_through_key', ], array_keys($contract->getAttributes()));
        });
    }

    // public function testIntermediateSoftDeletesAreIgnored()
    // {
    //     $this->seedData();
    //     HasOneThroughSoftDeletesTestUser::first()->delete();

    //     $contract = HasOneThroughSoftDeletesTestPosition::first()->contract;

    //     $this->assertSame('A title', $contract->title);
    // }

    public function testEagerLoadingLoadsRelatedModelsCorrectly()
    {
        $this->seedData();
        $position = HasOneThroughSoftDeletesTestPosition::with('contract')->first();

        $this->assertSame('ps', $position->shortname);
        $this->assertSame('A title', $position->contract->title);
    }

    /**
     * Helpers...
     */
    protected function seedData()
    {
        HasOneThroughTestPosition::create(['id' => 1, 'name' => 'President', 'shortname' => 'ps'])
            ->user()->create(['id' => 1, 'email' => 'taylorotwell@gmail.com', 'position_short' => 'ps'])
            ->contract()->create(['title' => 'A title', 'body' => 'A body', 'email' => 'taylorotwell@gmail.com']);
    }

    protected function seedDataExtended()
    {
        $position = HasOneThroughTestPosition::create(['id' => 2, 'name' => 'Vice President', 'shortname' => 'vp']);
        $position->user()->create(['id' => 2, 'email' => 'example1@gmail.com', 'position_short' => 'vp'])
            ->contract()->create(
                ['title' => 'Example1 title1', 'body' => 'Example1 body1', 'email' => 'example1contract1@gmail.com']
            );
    }

    /**
     * Seed data for a default HasOneThrough setup.
     */
    protected function seedDefaultData()
    {
        HasOneThroughDefaultTestPosition::create(['id' => 1, 'name' => 'President'])
            ->user()->create(['id' => 1, 'email' => 'taylorotwell@gmail.com'])
            ->contract()->create(['title' => 'A title', 'body' => 'A body']);
    }

    /**
     * Drop the default tables.
     */
    protected function resetDefault()
    {
        $this->schema()->dropTable('users_default');
        $this->schema()->dropTable('contracts_default');
        $this->schema()->dropTable('positions_default');
    }

    /**
     * Migrate tables for classes with a Laravel "default" HasOneThrough setup.
     */
    protected function migrateDefault()
    {
        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email' => ['type' => 'varchar', 'constraint' => 255, 'unique' => true],
            'has_one_through_default_test_position_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'unique' => true, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->createTable('users_default', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'has_one_through_default_test_user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'unique' => true],
            'title' => ['type' => 'varchar', 'constraint' => 255],
            'body' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->createTable('contracts_default', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->createTable('positions_default', true);
    }

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
class HasOneThroughTestUser extends Eloquent
{
    protected $table = 'users';
    protected $guarded = [];

    public function contract()
    {
        return $this->hasOne(HasOneThroughTestContract::class, 'user_id');
    }
}

/**
 * Eloquent Models...
 */
class HasOneThroughTestContract extends Eloquent
{
    protected $table = 'contracts';
    protected $guarded = [];

    public function owner()
    {
        return $this->belongsTo(HasOneThroughTestUser::class, 'user_id');
    }
}

class HasOneThroughTestPosition extends Eloquent
{
    protected $table = 'positions';
    protected $guarded = [];

    public function contract()
    {
        return $this->hasOneThrough(HasOneThroughTestContract::class, HasOneThroughTestUser::class, 'position_id', 'user_id');
    }

    public function user()
    {
        return $this->hasOne(HasOneThroughTestUser::class, 'position_id');
    }
}

/**
 * Eloquent Models...
 */
class HasOneThroughDefaultTestUser extends Eloquent
{
    protected $table = 'users_default';
    protected $guarded = [];

    public function contract()
    {
        return $this->hasOne(HasOneThroughDefaultTestContract::class);
    }
}

/**
 * Eloquent Models...
 */
class HasOneThroughDefaultTestContract extends Eloquent
{
    protected $table = 'contracts_default';
    protected $guarded = [];

    public function owner()
    {
        return $this->belongsTo(HasOneThroughDefaultTestUser::class);
    }
}

class HasOneThroughDefaultTestPosition extends Eloquent
{
    protected $table = 'positions_default';
    protected $guarded = [];

    public function contract()
    {
        return $this->hasOneThrough(HasOneThroughDefaultTestContract::class, HasOneThroughDefaultTestUser::class);
    }

    public function user()
    {
        return $this->hasOne(HasOneThroughDefaultTestUser::class);
    }
}

class HasOneThroughIntermediateTestPosition extends Eloquent
{
    protected $table = 'positions';
    protected $guarded = [];

    public function contract()
    {
        return $this->hasOneThrough(HasOneThroughTestContract::class, HasOneThroughTestUser::class, 'position_short', 'email', 'shortname', 'email');
    }

    public function user()
    {
        return $this->hasOne(HasOneThroughTestUser::class, 'position_id');
    }
}

class HasOneThroughSoftDeletesTestUser extends Eloquent
{
    use SoftDeletes;

    protected $table = 'users';
    protected $guarded = [];

    public function contract()
    {
        return $this->hasOne(HasOneThroughSoftDeletesTestContract::class, 'user_id');
    }
}

/**
 * Eloquent Models...
 */
class HasOneThroughSoftDeletesTestContract extends Eloquent
{
    protected $table = 'contracts';
    protected $guarded = [];

    public function owner()
    {
        return $this->belongsTo(HasOneThroughSoftDeletesTestUser::class, 'user_id');
    }
}

class HasOneThroughSoftDeletesTestPosition extends Eloquent
{
    protected $table = 'positions';
    protected $guarded = [];

    public function contract()
    {
        return $this->hasOneThrough(HasOneThroughSoftDeletesTestContract::class, HasOneThroughTestUser::class, 'position_id', 'user_id');
    }

    public function user()
    {
        return $this->hasOne(HasOneThroughSoftDeletesTestUser::class, 'position_id');
    }
}