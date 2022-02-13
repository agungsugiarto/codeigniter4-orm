<?php

namespace Fluent\Orm\Tests;

use CodeIgniter\Database\Config;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Fluent\Orm\Model;
use Tightenco\Collect\Support\Collection;

class DatabaseEloquentBelongsToManyChunkByIdTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    /**
     * Setup the database schema.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->createSchema();
    }

    protected function createSchema()
    {
        $this->schema()->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email'=> ['type' => 'varchar', 'constraint' => 255, 'unique' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('users', true);

        $this->schema()->addField([
            'aid' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'=> ['type' => 'varchar', 'constraint' => 255,],
        ])
        ->addPrimaryKey('aid')
        ->createTable('articles', true);

        $this->schema()->addField([
            'article_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
        ])
        ->addKey(['article_id', 'user_id'])
        ->addForeignKey('article_id', 'articles', 'aid')
        ->addForeignKey('user_id', 'users', 'id')
        ->createTable('article_user', true);
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
        $this->schema()->dropTable('articles', true);
        $this->schema()->dropTable('article_user', true);
    }

    public function testBelongsToChunkById()
    {
        $this->seedData();

        $user = BelongsToManyChunkByIdTestTestUser::query()->first();
        $i = 0;

        $user->articles()->chunkById(1, function (Collection $collection) use (&$i) {
            $i++;
            $this->assertEquals($i, $collection->first()->aid);
        });

        $this->assertSame(3, $i);
    }

    public function testGetDriverDatabase()
    {
        $this->markTestSkipped(sprintf("Database driver: %s", $this->db->DBDriver));
    }

    /**
     * Helpers...
     */
    protected function seedData()
    {
        $user = BelongsToManyChunkByIdTestTestUser::create(['id' => 1, 'email' => 'taylorotwell@gmail.com']);
        BelongsToManyChunkByIdTestTestArticle::insertBatch([
            ['aid' => 1, 'title' => 'Another title'],
            ['aid' => 2, 'title' => 'Another title'],
            ['aid' => 3, 'title' => 'Another title']
        ]);

        $user->articles()->sync([3, 1, 2]);
    }

    /**
     * Get a schema builder instance.
     *
     * @return \CodeIgniter\Database\Forge
     */
    protected function schema()
    {
        return Config::forge($this->DBGroup);
    }
}

class BelongsToManyChunkByIdTestTestUser extends Model
{
    protected $table = 'users';
    protected $fillable = ['id', 'email'];
    public $timestamps = false;

    public function articles()
    {
        return $this->belongsToMany(BelongsToManyChunkByIdTestTestArticle::class, 'article_user', 'user_id', 'article_id');
    }
}

class BelongsToManyChunkByIdTestTestArticle extends Model
{
    protected $primaryKey = 'aid';
    protected $table = 'articles';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['aid', 'title'];
}