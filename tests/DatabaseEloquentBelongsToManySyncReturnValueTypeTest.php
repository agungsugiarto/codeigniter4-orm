<?php

namespace Fluent\Orm\Tests;

use CodeIgniter\Database\Config;
use Fluent\Orm\Model;
use PHPUnit\Framework\TestCase;

class DatabaseEloquentBelongsToManySyncReturnValueTypeTest extends TestCase
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
            'email'=> ['type' => 'varchar', 'constraint' => 255, 'unique' => true],
        ])
        ->addPrimaryKey('id')
        ->createTable('users', true);

        $this->schema()->addField([
            'id' => ['type' => 'varchar', 'constraint' => 255],
            'title'=> ['type' => 'varchar', 'constraint' => 255,],
        ])
        ->addPrimaryKey('id')
        ->createTable('articles', true);

        $this->schema()->addField([
            'article_id' => ['type' => 'varchar', 'constraint' => 255],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'visible' => ['type' => 'tinyint', 'constraint' => 4, 'default' => false]
        ])
        ->addKey(['article_id', 'user_id'])
        ->addForeignKey('article_id', 'articles', 'id')
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
        $this->schema()->dropTable('users', true);
        $this->schema()->dropTable('articles', true);
        $this->schema()->dropTable('article_user', true);
    }

    public function testSyncReturnValueType()
    {
        $this->seedData();

        $user = BelongsToManySyncTestTestUser::query()->first();
        $articleIDs = BelongsToManySyncTestTestArticle::all()->pluck('id')->toArray();

        $changes = $user->articles()->sync($articleIDs);

        collect($changes['attached'])->map(function ($id) {
            $this->assertSame(gettype($id), (new BelongsToManySyncTestTestArticle)->getKeyType());
        });

        $user->articles->each(function (BelongsToManySyncTestTestArticle $article) {
            $this->assertEquals('0', $article->pivot->visible);
        });
    }

    public function testSyncWithPivotDefaultsReturnValueType()
    {
        $this->seedData();

        $user = BelongsToManySyncTestTestUser::query()->first();
        $articleIDs = BelongsToManySyncTestTestArticle::all()->pluck('id')->toArray();

        $changes = $user->articles()->syncWithPivotValues($articleIDs, ['visible' => true]);

        collect($changes['attached'])->each(function ($id) {
            $this->assertSame(gettype($id), (new BelongsToManySyncTestTestArticle)->getKeyType());
        });

        $user->articles->each(function (BelongsToManySyncTestTestArticle $article) {
            $this->assertEquals('1', $article->pivot->visible);
        });
    }

    /**
     * Helpers...
     */
    protected function seedData()
    {
        BelongsToManySyncTestTestUser::create(['id' => 1, 'email' => 'taylorotwell@gmail.com']);
        BelongsToManySyncTestTestArticle::insertBatch([
            ['id' => '7b7306ae-5a02-46fa-a84c-9538f45c7dd4', 'title' => 'uuid title'],
            ['id' => (string) (PHP_INT_MAX + 1), 'title' => 'Another title'],
            ['id' => '1', 'title' => 'Another title'],
        ]);
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

class BelongsToManySyncTestTestUser extends Model
{
    protected $table = 'users';
    protected $fillable = ['id', 'email'];
    public $timestamps = false;

    public function articles()
    {
        return $this->belongsToMany(BelongsToManySyncTestTestArticle::class, 'article_user', 'user_id', 'article_id')->withPivot('visible');
    }
}

class BelongsToManySyncTestTestArticle extends Model
{
    protected $table = 'articles';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id', 'title'];
}