<?php

namespace Fluent\Orm\Tests\Database\Migrations;

use CodeIgniter\Database\Migration;

class Create4 extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email' => ['type' => 'varchar', 'constraint' => 255, 'unique' => true],
            'position_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'unique' => true, 'null' => true],
            'position_short' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users_4', true);

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'varchar', 'constraint' => 255],
            'title' => ['type' => 'varchar', 'constraint' => 255],
            'body' => ['type' => 'varchar', 'constraint' => 255],
            'email' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('contracts', true);

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'varchar', 'constraint' => 255],
            'shortname' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('positions', true);

        // ---------------------------------------------------------------------------------------------

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email' => ['type' => 'varchar', 'constraint' => 255, 'unique' => true],
            'has_one_through_default_test_position_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'unique' => true, 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users_4_default', true);

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'has_one_through_default_test_user_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'unique' => true],
            'title' => ['type' => 'varchar', 'constraint' => 255],
            'body' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('contracts_default', true);

        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'varchar', 'constraint' => 255],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('positions_default', true);
    }

    public function down()
    {
        $this->forge->dropTable('users_4', true);
        $this->forge->dropTable('contracts', true);
        $this->forge->dropTable('positions', true);

        $this->forge->dropTable('users_4_default', true);
        $this->forge->dropTable('contracts_default', true);
        $this->forge->dropTable('positions_default', true);
    }
}
