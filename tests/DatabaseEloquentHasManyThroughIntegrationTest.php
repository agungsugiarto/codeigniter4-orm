<?php

namespace Fluent\Orm\Tests;

use CodeIgniter\Database\Config;
use Fluent\Orm\Model as Eloquent;
use Fluent\Orm\Exceptions\ModelNotFoundException;
use Fluent\Orm\SoftDeletes;
use Tightenco\Collect\Support\Collection;
use Tightenco\Collect\Support\LazyCollection;
use PHPUnit\Framework\TestCase;

class DatabaseEloquentHasManyThroughIntegrationTest extends TestCase
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
            'email' => ['type' => 'varchar', 'constraint' => 255],
            'country_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'country_short' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('users', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'title' => ['type' => 'varchar', 'constraint' => 255],
            'body' => ['type' => 'varchar', 'constraint' => 255],
            'email' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('posts', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'varchar', 'constraint' => 255],
            'shortname' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('countries', true);
    }

    /**
     * Tear down the database schema.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->schema()->dropTable('users');
        $this->schema()->dropTable('posts');
        $this->schema()->dropTable('countries');
    }

    public function testItLoadsAHasManyThroughRelationWithCustomKeys()
    {
        $this->seedData();
        $posts = HasManyThroughTestCountry::first()->posts;

        $this->assertSame('A title', $posts[0]->title);
        $this->assertCount(2, $posts);
    }

    public function testItLoadsADefaultHasManyThroughRelation()
    {
        $this->migrateDefault();
        $this->seedDefaultData();

        $posts = HasManyThroughDefaultTestCountry::first()->posts;
        $this->assertSame('A title', $posts[0]->title);
        $this->assertCount(2, $posts);

        $this->resetDefault();
    }

    public function testItLoadsARelationWithCustomIntermediateAndLocalKey()
    {
        $this->seedData();
        $posts = HasManyThroughIntermediateTestCountry::first()->posts;

        $this->assertSame('A title', $posts[0]->title);
        $this->assertCount(2, $posts);
    }

    public function testEagerLoadingARelationWithCustomIntermediateAndLocalKey()
    {
        $this->seedData();
        $posts = HasManyThroughIntermediateTestCountry::with('posts')->first()->posts;

        $this->assertSame('A title', $posts[0]->title);
        $this->assertCount(2, $posts);
    }

    public function testWhereHasOnARelationWithCustomIntermediateAndLocalKey()
    {
        $this->seedData();
        $country = HasManyThroughIntermediateTestCountry::whereHas('posts', function ($query) {
            $query->where('title', 'A title');
        })->get();

        $this->assertCount(1, $country);
    }

    public function testFindMethod()
    {
        HasManyThroughTestCountry::create(['id' => 1, 'name' => 'United States of America', 'shortname' => 'us'])
                                 ->users()->create(['id' => 1, 'email' => 'taylorotwell@gmail.com', 'country_short' => 'us'])
                                 ->posts()->createMany([
                                     ['id' => 1, 'title' => 'A title', 'body' => 'A body', 'email' => 'taylorotwell@gmail.com'],
                                     ['id' => 2, 'title' => 'Another title', 'body' => 'Another body', 'email' => 'taylorotwell@gmail.com'],
                                 ]);

        $country = HasManyThroughTestCountry::first();
        $post = $country->posts()->find(1);

        $this->assertNotNull($post);
        $this->assertSame('A title', $post->title);

        $this->assertCount(2, $country->posts()->find([1, 2]));
        $this->assertCount(2, $country->posts()->find(new Collection([1, 2])));
    }

    public function testFindManyMethod()
    {
        HasManyThroughTestCountry::create(['id' => 1, 'name' => 'United States of America', 'shortname' => 'us'])
                                 ->users()->create(['id' => 1, 'email' => 'taylorotwell@gmail.com', 'country_short' => 'us'])
                                 ->posts()->createMany([
                                     ['id' => 1, 'title' => 'A title', 'body' => 'A body', 'email' => 'taylorotwell@gmail.com'],
                                     ['id' => 2, 'title' => 'Another title', 'body' => 'Another body', 'email' => 'taylorotwell@gmail.com'],
                                 ]);

        $country = HasManyThroughTestCountry::first();

        $this->assertCount(2, $country->posts()->findMany([1, 2]));
        $this->assertCount(2, $country->posts()->findMany(new Collection([1, 2])));
    }

    public function testFirstOrFailThrowsAnException()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('No query results for model [Fluent\Orm\Tests\HasManyThroughTestPost].');

        HasManyThroughTestCountry::create(['id' => 1, 'name' => 'United States of America', 'shortname' => 'us'])
            ->users()->create(['id' => 1, 'email' => 'taylorotwell@gmail.com', 'country_short' => 'us']);

        HasManyThroughTestCountry::first()->posts()->firstOrFail();
    }

    public function testFindOrFailThrowsAnException()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('No query results for model [Fluent\Orm\Tests\HasManyThroughTestPost] 1');

        HasManyThroughTestCountry::create(['id' => 1, 'name' => 'United States of America', 'shortname' => 'us'])
                                 ->users()->create(['id' => 1, 'email' => 'taylorotwell@gmail.com', 'country_short' => 'us']);

        HasManyThroughTestCountry::first()->posts()->findOrFail(1);
    }

    public function testFindOrFailWithManyThrowsAnException()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('No query results for model [Fluent\Orm\Tests\HasManyThroughTestPost] 1, 2');

        HasManyThroughTestCountry::create(['id' => 1, 'name' => 'United States of America', 'shortname' => 'us'])
                                 ->users()->create(['id' => 1, 'email' => 'taylorotwell@gmail.com', 'country_short' => 'us'])
                                 ->posts()->create(['id' => 1, 'title' => 'A title', 'body' => 'A body', 'email' => 'taylorotwell@gmail.com']);

        HasManyThroughTestCountry::first()->posts()->findOrFail([1, 2]);
    }

    public function testFindOrFailWithManyUsingCollectionThrowsAnException()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('No query results for model [Fluent\Orm\Tests\HasManyThroughTestPost] 1, 2');

        HasManyThroughTestCountry::create(['id' => 1, 'name' => 'United States of America', 'shortname' => 'us'])
                                 ->users()->create(['id' => 1, 'email' => 'taylorotwell@gmail.com', 'country_short' => 'us'])
                                 ->posts()->create(['id' => 1, 'title' => 'A title', 'body' => 'A body', 'email' => 'taylorotwell@gmail.com']);

        HasManyThroughTestCountry::first()->posts()->findOrFail(new Collection([1, 2]));
    }

    public function testFirstRetrievesFirstRecord()
    {
        $this->seedData();
        $post = HasManyThroughTestCountry::first()->posts()->first();

        $this->assertNotNull($post);
        $this->assertSame('A title', $post->title);
    }

    public function testAllColumnsAreRetrievedByDefault()
    {
        $this->seedData();
        $post = HasManyThroughTestCountry::first()->posts()->first();
        $this->assertEquals([
            'id',
            'user_id',
            'title',
            'body',
            'email',
            'created_at',
            'updated_at',
            'laravel_through_key',
        ], array_keys($post->getAttributes()));
    }

    public function testOnlyProperColumnsAreSelectedIfProvided()
    {
        $this->seedData();
        $post = HasManyThroughTestCountry::first()->posts()->first(['title', 'body']);

        $this->assertEquals([
            'title',
            'body',
            'laravel_through_key',
        ], array_keys($post->getAttributes()));
    }

    public function testChunkReturnsCorrectModels()
    {
        $this->seedData();
        $this->seedDataExtended();
        $country = HasManyThroughTestCountry::find(2);

        $country->posts()->chunk(10, function ($postsChunk) {
            $post = $postsChunk->first();
            $this->assertEquals([
                'id',
                'user_id',
                'title',
                'body',
                'email',
                'created_at',
                'updated_at',
                'laravel_through_key',
            ], array_keys($post->getAttributes()));
        });
    }

    public function testChunkById()
    {
        $this->seedData();
        $this->seedDataExtended();
        $country = HasManyThroughTestCountry::find(2);

        $i = 0;
        $count = 0;

        $country->posts()->chunkById(2, function ($collection) use (&$i, &$count) {
            $i++;
            $count += $collection->count();
        });

        $this->assertEquals(3, $i);
        $this->assertEquals(6, $count);
    }

    public function testCursorReturnsCorrectModels()
    {
        $this->seedData();
        $this->seedDataExtended();
        $country = HasManyThroughTestCountry::find(2);

        $posts = $country->posts()->cursor();

        $this->assertInstanceOf(LazyCollection::class, $posts);

        foreach ($posts as $post) {
            $this->assertEquals([
                'id',
                'user_id',
                'title',
                'body',
                'email',
                'created_at',
                'updated_at',
                'laravel_through_key',
            ], array_keys($post->getAttributes()));
        }
    }

    public function testEachReturnsCorrectModels()
    {
        $this->seedData();
        $this->seedDataExtended();
        $country = HasManyThroughTestCountry::find(2);

        $country->posts()->each(function ($post) {
            $this->assertEquals([
                'id',
                'user_id',
                'title',
                'body',
                'email',
                'created_at',
                'updated_at',
                'laravel_through_key',
            ], array_keys($post->getAttributes()));
        });
    }

    public function testLazyReturnsCorrectModels()
    {
        $this->seedData();
        $this->seedDataExtended();
        $country = HasManyThroughTestCountry::find(2);

        $country->posts()->lazy(10)->each(function ($post) {
            $this->assertEquals([
                'id',
                'user_id',
                'title',
                'body',
                'email',
                'created_at',
                'updated_at',
                'laravel_through_key',
            ], array_keys($post->getAttributes()));
        });
    }

    public function testLazyById()
    {
        $this->seedData();
        $this->seedDataExtended();
        $country = HasManyThroughTestCountry::find(2);

        $i = 0;

        $country->posts()->lazyById(2)->each(function ($post) use (&$i, &$count) {
            $i++;

            $this->assertEquals([
                'id',
                'user_id',
                'title',
                'body',
                'email',
                'created_at',
                'updated_at',
                'laravel_through_key',
            ], array_keys($post->getAttributes()));
        });

        $this->assertEquals(6, $i);
    }

    // public function testIntermediateSoftDeletesAreIgnored()
    // {
    //     $this->seedData();
    //     HasManyThroughSoftDeletesTestUser::first()->delete();

    //     $posts = HasManyThroughSoftDeletesTestCountry::first()->posts;

    //     $this->assertSame('A title', $posts[0]->title);
    //     $this->assertCount(2, $posts);
    // }

    public function testEagerLoadingLoadsRelatedModelsCorrectly()
    {
        $this->seedData();
        $country = HasManyThroughSoftDeletesTestCountry::with('posts')->first();

        $this->assertSame('us', $country->shortname);
        $this->assertSame('A title', $country->posts[0]->title);
        $this->assertCount(2, $country->posts);
    }

    /**
     * Helpers...
     */
    protected function seedData()
    {
        HasManyThroughTestCountry::create(['id' => 1, 'name' => 'United States of America', 'shortname' => 'us'])
                                 ->users()->create(['id' => 1, 'email' => 'taylorotwell@gmail.com', 'country_short' => 'us'])
                                 ->posts()->createMany([
                                     ['title' => 'A title', 'body' => 'A body', 'email' => 'taylorotwell@gmail.com'],
                                     ['title' => 'Another title', 'body' => 'Another body', 'email' => 'taylorotwell@gmail.com'],
                                 ]);
    }

    protected function seedDataExtended()
    {
        $country = HasManyThroughTestCountry::create(['id' => 2, 'name' => 'United Kingdom', 'shortname' => 'uk']);
        $country->users()->create(['id' => 2, 'email' => 'example1@gmail.com', 'country_short' => 'uk'])
            ->posts()->createMany([
                ['title' => 'Example1 title1', 'body' => 'Example1 body1', 'email' => 'example1post1@gmail.com'],
                ['title' => 'Example1 title2', 'body' => 'Example1 body2', 'email' => 'example1post2@gmail.com'],
            ]);
        $country->users()->create(['id' => 3, 'email' => 'example2@gmail.com', 'country_short' => 'uk'])
            ->posts()->createMany([
                ['title' => 'Example2 title1', 'body' => 'Example2 body1', 'email' => 'example2post1@gmail.com'],
                ['title' => 'Example2 title2', 'body' => 'Example2 body2', 'email' => 'example2post2@gmail.com'],
            ]);
        $country->users()->create(['id' => 4, 'email' => 'example3@gmail.com', 'country_short' => 'uk'])
            ->posts()->createMany([
                ['title' => 'Example3 title1', 'body' => 'Example3 body1', 'email' => 'example3post1@gmail.com'],
                ['title' => 'Example3 title2', 'body' => 'Example3 body2', 'email' => 'example3post2@gmail.com'],
            ]);
    }

    /**
     * Seed data for a default HasManyThrough setup.
     */
    protected function seedDefaultData()
    {
        HasManyThroughDefaultTestCountry::create(['id' => 1, 'name' => 'United States of America'])
                                        ->users()->create(['id' => 1, 'email' => 'taylorotwell@gmail.com'])
                                        ->posts()->createMany([
                                            ['title' => 'A title', 'body' => 'A body'],
                                            ['title' => 'Another title', 'body' => 'Another body'],
                                        ]);
    }

    /**
     * Drop the default tables.
     */
    protected function resetDefault()
    {
        $this->schema()->dropTable('users_default');
        $this->schema()->dropTable('posts_default');
        $this->schema()->dropTable('countries_default');
    }

    /**
     * Migrate tables for classes with a Laravel "default" HasManyThrough setup.
     */
    protected function migrateDefault()
    {
        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'has_many_through_default_test_country_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'email' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('users_default', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'has_many_through_default_test_user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'title' => ['type' => 'varchar', 'constraint' => 255],
            'body' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('posts_default', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('countries_default', true);
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
class HasManyThroughTestUser extends Eloquent
{
    protected $table = 'users';
    protected $guarded = [];

    public function posts()
    {
        return $this->hasMany(HasManyThroughTestPost::class, 'user_id');
    }
}

/**
 * Eloquent Models...
 */
class HasManyThroughTestPost extends Eloquent
{
    protected $table = 'posts';
    protected $guarded = [];

    public function owner()
    {
        return $this->belongsTo(HasManyThroughTestUser::class, 'user_id');
    }
}

class HasManyThroughTestCountry extends Eloquent
{
    protected $table = 'countries';
    protected $guarded = [];

    public function posts()
    {
        return $this->hasManyThrough(HasManyThroughTestPost::class, HasManyThroughTestUser::class, 'country_id', 'user_id');
    }

    public function users()
    {
        return $this->hasMany(HasManyThroughTestUser::class, 'country_id');
    }
}

/**
 * Eloquent Models...
 */
class HasManyThroughDefaultTestUser extends Eloquent
{
    protected $table = 'users_default';
    protected $guarded = [];

    public function posts()
    {
        return $this->hasMany(HasManyThroughDefaultTestPost::class);
    }
}

/**
 * Eloquent Models...
 */
class HasManyThroughDefaultTestPost extends Eloquent
{
    protected $table = 'posts_default';
    protected $guarded = [];

    public function owner()
    {
        return $this->belongsTo(HasManyThroughDefaultTestUser::class);
    }
}

class HasManyThroughDefaultTestCountry extends Eloquent
{
    protected $table = 'countries_default';
    protected $guarded = [];

    public function posts()
    {
        return $this->hasManyThrough(HasManyThroughDefaultTestPost::class, HasManyThroughDefaultTestUser::class);
    }

    public function users()
    {
        return $this->hasMany(HasManyThroughDefaultTestUser::class);
    }
}

class HasManyThroughIntermediateTestCountry extends Eloquent
{
    protected $table = 'countries';
    protected $guarded = [];

    public function posts()
    {
        return $this->hasManyThrough(HasManyThroughTestPost::class, HasManyThroughTestUser::class, 'country_short', 'email', 'shortname', 'email');
    }

    public function users()
    {
        return $this->hasMany(HasManyThroughTestUser::class, 'country_id');
    }
}

class HasManyThroughSoftDeletesTestUser extends Eloquent
{
    use SoftDeletes;

    protected $table = 'users';
    protected $guarded = [];

    public function posts()
    {
        return $this->hasMany(HasManyThroughSoftDeletesTestPost::class, 'user_id');
    }
}

/**
 * Eloquent Models...
 */
class HasManyThroughSoftDeletesTestPost extends Eloquent
{
    protected $table = 'posts';
    protected $guarded = [];

    public function owner()
    {
        return $this->belongsTo(HasManyThroughSoftDeletesTestUser::class, 'user_id');
    }
}

class HasManyThroughSoftDeletesTestCountry extends Eloquent
{
    protected $table = 'countries';
    protected $guarded = [];

    public function posts()
    {
        return $this->hasManyThrough(HasManyThroughSoftDeletesTestPost::class, HasManyThroughTestUser::class, 'country_id', 'user_id');
    }

    public function users()
    {
        return $this->hasMany(HasManyThroughSoftDeletesTestUser::class, 'country_id');
    }
}