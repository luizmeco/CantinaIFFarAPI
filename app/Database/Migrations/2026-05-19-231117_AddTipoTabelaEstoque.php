<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipoTabelaEstoque extends Migration
{
    public function up()
    {
        $this->forge->addColumn('estoques', [
            'tipo' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'null' => true,
                'comment' => 'entrada/saída'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('estoques', 'tipo');
    }
}
