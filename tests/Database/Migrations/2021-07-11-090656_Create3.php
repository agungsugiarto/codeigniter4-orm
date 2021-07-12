<?php

namespace Fluent\Orm\Tests\Database\Migrations;

use CodeIgniter\Database\Migration;

class Create3 extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users_3', true);

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('logins', true);

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'state' => ['type' => 'varchar', 'constraint' => 255],
            'type' => ['type' => 'varchar', 'constraint' => 255],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('states', true);

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'published_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('prices', true);
    }

    public function down()
    {
        $this->forge->dropTable('users_3', true);
        $this->forge->dropTable('logins', true);
        $this->forge->dropTable('states', true);
        $this->forge->dropTable('prices', true);
    }
}
