<?php

namespace Fluent\Orm\Tests;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Fluent\Orm\Model;

class DatabaseEloquentBelongsToManySyncReturnValueTypeTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    /** {@inheritdoc} */
    protected $namespace = 'Fluent\Orm\Tests';

    /** {@inheritdoc} */
    protected $refresh = true;

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
}

class BelongsToManySyncTestTestUser extends Model
{
    protected $table = 'users_1';
    protected $fillable = ['id', 'email'];
    public $timestamps = false;

    public function articles()
    {
        return $this->belongsToMany(BelongsToManySyncTestTestArticle::class, 'article_user_1', 'user_id', 'article_id')->withPivot('visible');
    }
}

class BelongsToManySyncTestTestArticle extends Model
{
    protected $table = 'articles_1';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id', 'title'];
}