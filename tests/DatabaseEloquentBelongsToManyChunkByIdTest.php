<?php

namespace Fluent\Orm\Tests;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Fluent\Orm\Model;
use Tightenco\Collect\Support\Collection;

class DatabaseEloquentBelongsToManyChunkByIdTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    /** {@inheritdoc} */
    protected $namespace = 'Fluent\Orm\Tests';

    /** {@inheritdoc} */
    protected $refresh = true;

    /** {@inheritdoc} */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /** {@inheritdoc} */
    protected function tearDown(): void
    {
        parent::tearDown();
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