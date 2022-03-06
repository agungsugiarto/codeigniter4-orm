<?php

namespace Fluent\Orm\Tests;

use CodeIgniter\Database\Config;
use Fluent\Orm\Model;
use PHPUnit\Framework\TestCase;

class DatabaseEloquentPolymorphicIntegrationTest extends TestCase
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
            'email' => ['type' => 'varchar', 'constraint' => 255, 'unique' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('users', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'title' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'body' => ['type' => 'text', 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('posts', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'commentable_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'commentable_type' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'body' => ['type' => 'text', 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('comments', true);

        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'likeable_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'likeable_type' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('likes', true);
    }

    /**
     * Tear down the database schema.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->schema()->dropTable('users', true);
        $this->schema()->dropTable('posts', true);
        $this->schema()->dropTable('comments', true);
        $this->schema()->dropTable('likes', true);
    }

    // public function testItLoadsRelationshipsAutomatically()
    // {
    //     $this->seedData();

    //     $like = TestLikeWithSingleWith::first();

    //     $this->assertTrue($like->relationLoaded('likeable'));
    //     $this->assertEquals(TestComment::first(), $like->likeable);
    // }

    // public function testItLoadsChainedRelationshipsAutomatically()
    // {
    //     $this->seedData();

    //     $like = TestLikeWithSingleWith::first();

    //     $this->assertTrue($like->likeable->relationLoaded('commentable'));
    //     $this->assertEquals(TestPost::first(), $like->likeable->commentable);
    // }

    // public function testItLoadsNestedRelationshipsAutomatically()
    // {
    //     $this->seedData();

    //     $like = TestLikeWithNestedWith::first();

    //     $this->assertTrue($like->relationLoaded('likeable'));
    //     $this->assertTrue($like->likeable->relationLoaded('owner'));

    //     $this->assertEquals(TestUser::first(), $like->likeable->owner);
    // }

    // public function testItLoadsNestedRelationshipsOnDemand()
    // {
    //     $this->seedData();

    //     $like = TestLike::with('likeable.owner')->first();

    //     $this->assertTrue($like->relationLoaded('likeable'));
    //     $this->assertTrue($like->likeable->relationLoaded('owner'));

    //     $this->assertEquals(TestUser::first(), $like->likeable->owner);
    // }

    // public function testItLoadsNestedMorphRelationshipsOnDemand()
    // {
    //     $this->seedData();

    //     TestPost::first()->likes()->create([]);

    //     $likes = TestLike::with('likeable.owner')->get()->loadMorph('likeable', [
    //         TestComment::class => ['commentable'],
    //         TestPost::class => 'comments',
    //     ]);

    //     $this->assertTrue($likes[0]->relationLoaded('likeable'));
    //     $this->assertTrue($likes[0]->likeable->relationLoaded('owner'));
    //     $this->assertTrue($likes[0]->likeable->relationLoaded('commentable'));

    //     $this->assertTrue($likes[1]->relationLoaded('likeable'));
    //     $this->assertTrue($likes[1]->likeable->relationLoaded('owner'));
    //     $this->assertTrue($likes[1]->likeable->relationLoaded('comments'));
    // }

    // public function testItLoadsNestedMorphRelationshipCountsOnDemand()
    // {
    //     $this->seedData();

    //     TestPost::first()->likes()->create([]);
    //     TestComment::first()->likes()->create([]);

    //     $likes = TestLike::with('likeable.owner')->get()->loadMorphCount('likeable', [
    //         TestComment::class => ['likes'],
    //         TestPost::class => 'comments',
    //     ]);

    //     $this->assertTrue($likes[0]->relationLoaded('likeable'));
    //     $this->assertTrue($likes[0]->likeable->relationLoaded('owner'));
    //     $this->assertEquals(2, $likes[0]->likeable->likes_count);

    //     $this->assertTrue($likes[1]->relationLoaded('likeable'));
    //     $this->assertTrue($likes[1]->likeable->relationLoaded('owner'));
    //     $this->assertEquals(1, $likes[1]->likeable->comments_count);

    //     $this->assertTrue($likes[2]->relationLoaded('likeable'));
    //     $this->assertTrue($likes[2]->likeable->relationLoaded('owner'));
    //     $this->assertEquals(2, $likes[2]->likeable->likes_count);
    // }

    /**
     * Helpers...
     */
    protected function seedData()
    {
        $taylor = TestUser::create(['id' => 1, 'email' => 'taylorotwell@gmail.com']);

        $taylor->posts()->create(['title' => 'A title', 'body' => 'A body'])
            ->comments()->create(['body' => 'A comment body', 'user_id' => 1])
            ->likes()->create([]);
    }

    protected function schema()
    {
        return Config::forge();
    }
}

/**
 * Eloquent Models...
 */
class TestUser extends Model
{
    protected $table = 'users';
    protected $guarded = [];

    public function posts()
    {
        return $this->hasMany(TestPost::class, 'user_id');
    }
}

/**
 * Eloquent Models...
 */
class TestPost extends Model
{
    protected $table = 'posts';
    protected $guarded = [];

    public function comments()
    {
        return $this->morphMany(TestComment::class, 'commentable');
    }

    public function owner()
    {
        return $this->belongsTo(TestUser::class, 'user_id');
    }

    public function likes()
    {
        return $this->morphMany(TestLike::class, 'likeable');
    }
}

/**
 * Eloquent Models...
 */
class TestComment extends Model
{
    protected $table = 'comments';
    protected $guarded = [];
    protected $with = ['commentable'];

    public function owner()
    {
        return $this->belongsTo(TestUser::class, 'user_id');
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function likes()
    {
        return $this->morphMany(TestLike::class, 'likeable');
    }
}

class TestLike extends Model
{
    protected $table = 'likes';
    protected $guarded = [];

    public function likeable()
    {
        return $this->morphTo();
    }
}

class TestLikeWithSingleWith extends Model
{
    protected $table = 'likes';
    protected $guarded = [];
    protected $with = ['likeable'];

    public function likeable()
    {
        return $this->morphTo();
    }
}

class TestLikeWithNestedWith extends Model
{
    protected $table = 'likes';
    protected $guarded = [];
    protected $with = ['likeable.owner'];

    public function likeable()
    {
        return $this->morphTo();
    }
}