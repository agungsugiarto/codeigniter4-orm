<?php

namespace Fluent\Orm\Tests;

use CodeIgniter\Database\Config;
use Fluent\Orm\Collection;
use Fluent\Orm\Model as Eloquent;
use Fluent\Orm\Relations\Relation;
use PHPUnit\Framework\TestCase;

class DatabaseEloquentIntegrationWithTablePrefixTest extends TestCase
{
    /**
     * Bootstrap Eloquent.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->connection()->setPrefix('prefix_');

        $this->createSchema();
    }

    protected function createSchema()
    {
        $this->schema('tests')->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->createTable('users', true);

        $this->schema('tests')->addField([
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'friend_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
        ])
        ->createTable('friends', true);

        $this->schema('tests')->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'parent_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'name' => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->createTable('posts', true);

        $this->schema('tests')->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'imageable_type' => ['type' => 'varchar', 'constraint' => 255],
            'imageable_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'name' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ])
        ->createTable('photos', true);
    }

    /**
     * Tear down the database schema.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        foreach (['tests'] as $connection) {
            $this->schema($connection)->dropTable('users');
            $this->schema($connection)->dropTable('friends');
            $this->schema($connection)->dropTable('posts');
            $this->schema($connection)->dropTable('photos');
        }

        Relation::morphMap([], false);
    }

    public function testBasicModelHydration()
    {
        EloquentTestUser::create(['email' => 'taylorotwell@gmail.com']);
        EloquentTestUser::create(['email' => 'abigailotwell@gmail.com']);

        $models = EloquentTestUser::fromQuery('SELECT * FROM prefix_users WHERE email = ?', ['abigailotwell@gmail.com']);

        $this->assertInstanceOf(Collection::class, $models);
        $this->assertInstanceOf(EloquentTestUser::class, $models[0]);
        $this->assertSame('abigailotwell@gmail.com', $models[0]->email);
        $this->assertCount(1, $models);
    }

    /**
     * Helpers...
     */

    /**
     * Get a database connection instance.
     *
     * @return \CodeIgniter\Database\BaseConnection
     */
    protected function connection($connection = 'tests')
    {
        return Eloquent::resolveConnection($connection);
    }

    /**
     * Get a schema builder instance.
     *
     * @return \CodeIgniter\Database\Forge
     */
    protected function schema($connection = 'tests')
    {
        return Config::forge($connection);
    }
}