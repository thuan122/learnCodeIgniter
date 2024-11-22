<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddClient extends Migration
{
    public function up()
    {
        // addField first, then createTable later
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
                'unique' => true
            ],
            'retainer_fee' => [
                'type' => 'INT',
                'constraint' => 100,
                'null' => false,
                'unique' => true
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'created_at datetime default current_timestamp',
        ]);
        $this->forge->addPrimaryKey('id');
        // need ['ENGINE' => 'InnoDB'] to temporarily fix
        // Specified key was too long; max key length is 1000 bytes
        $this->forge->createTable('client', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('client');
    }
}
