<?php

namespace Fluent\Orm\Tests\Database\Migrations;

use CodeIgniter\Database\Migration;

class DatabaseEloquentBelongsToManyChunkByIdTest extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email'=> ['type' => 'varchar', 'constraint' => 255, 'unique' => true,],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users', true);

        $this->forge->addField([
            'aid' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'=> ['type' => 'varchar', 'constraint' => 255,],
        ]);
        $this->forge->addPrimaryKey('aid');
        $this->forge->createTable('articles', true);

        $this->forge->addField([
            'article_id'       => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addKey(['article_id', 'user_id']);
        $this->forge->addForeignKey('article_id', 'articles', 'aid');
        $this->forge->addForeignKey('user_id', 'users', 'id');
        $this->forge->createTable('article_user', true);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('articles', true);
        $this->forge->dropTable('article_user', true);
    }
}
