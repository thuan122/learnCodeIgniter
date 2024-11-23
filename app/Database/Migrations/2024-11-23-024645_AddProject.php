<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProject extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "constraint" => 5,
                "unsigned" => true,
                "auto_increment" => true
            ],
            "user_id" => [
                "type" => "INT",
                "constraint" => 5,
            ],
            "title" => [
                "type" => "VARCHAR",
                "constraint" => 150,
                "null" => false
            ],
            "budget" => [
                "type" => "INT",
                "constraint" => 5,
            ]
        ]);
        $this->forge->addPrimaryKey("id");
        // By using InnoDB engine it will
        // temporarily fix for "Specified key was too long; max key length is 1000 bytes"  
        $this->forge->createTable("projects", true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable("projects");
    }
}
