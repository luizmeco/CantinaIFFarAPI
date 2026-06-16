<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TabelaEstoque extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_produto' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'quantidade' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'fornecedor' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'observacao' => [
                'type' => 'varchar',
                'constraint' => 255,
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
        $this->forge->createTable('estoques');
    }

    public function down()
    {
        $this->forge->dropTable('estoques');
    }
}
    

