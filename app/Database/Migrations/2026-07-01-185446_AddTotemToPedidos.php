<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTotemToPedidos extends Migration
{
    public function up()
    {
        $fields = [
            'totem' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'status'
            ]
        ];
        $this->forge->addColumn('pedidos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pedidos', 'totem');
    }
}
