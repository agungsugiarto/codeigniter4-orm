<?php

namespace Fluent\Orm\Tests\Database\Migrations;

use CodeIgniter\Database\Migration;

class Create1 extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email'=> ['type' => 'varchar', 'constraint' => 255, 'unique' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users_1', true);

        $this->forge->addField([
            'id' => ['type' => 'varchar', 'constraint' => 255],
            'title'=> ['type' => 'varchar', 'constraint' => 255,],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('articles_1', true);

        $this->forge->addField([
            'article_id' => ['type' => 'varchar', 'constraint' => 255],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'visible' => ['type' => 'tinyint', 'constraint' => 4, 'default' => false]
        ]);
        $this->forge->addKey(['article_id', 'user_id']);
        $this->forge->addForeignKey('article_id', 'articles_1', 'id');
        $this->forge->addForeignKey('user_id', 'users_1', 'id');
        $this->forge->createTable('article_user_1', true);
    }

    public function down()
    {
        $this->forge->dropTable('users_1', true);
        $this->forge->dropTable('articles_1', true);
        $this->forge->dropTable('article_user_1', true);
    }
}
