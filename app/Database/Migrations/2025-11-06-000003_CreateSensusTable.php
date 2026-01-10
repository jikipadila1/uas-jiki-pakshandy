<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSensusTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nik' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'nama_penduduk' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'kota_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tanggal_input' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kota_id', 'cities', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sensus');
    }

    public function down()
    {
        $this->forge->dropTable('sensus');
    }
}