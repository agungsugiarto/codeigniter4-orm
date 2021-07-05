<?php

namespace Fluent\Orm\Tests;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Fluent\Orm\Model;

class DatabaseEloquentBelongsToManyLazyByIdTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    /** {@inheritdoc} */
    protected $namespace = 'Fluent\Orm\Tests';

    public function testBelongsToLazyById()
    {
        $this->seedData();

        $user = BelongsToManyLazyByIdTestTestUser::query()->first();
        $i = 0;

        $user->articles()->lazyById(1)->each(function ($model) use (&$i) {
            $i++;
            $this->assertEquals($i, $model->aid);
        });

        $this->assertSame(3, $i);
    }

    /**
     * Helpers...
     */
    protected function seedData()
    {
        $user = BelongsToManyLazyByIdTestTestUser::create(['id' => 1, 'email' => 'taylorotwell@gmail.com']);
        BelongsToManyLazyByIdTestTestArticle::query()->insertBatch([
            ['aid' => 1, 'title' => 'Another title'],
            ['aid' => 2, 'title' => 'Another title'],
            ['aid' => 3, 'title' => 'Another title'],
        ]);

        $user->articles()->sync([3, 1, 2]);
    }
}

class BelongsToManyLazyByIdTestTestUser extends Model
{
    protected $table = 'users';
    protected $fillable = ['id', 'email'];
    public $timestamps = false;

    public function articles()
    {
        return $this->belongsToMany(BelongsToManyLazyByIdTestTestArticle::class, 'article_user', 'user_id', 'article_id');
    }
}

class BelongsToManyLazyByIdTestTestArticle extends Model
{
    protected $primaryKey = 'aid';
    protected $table = 'articles';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['aid', 'title'];
}