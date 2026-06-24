<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBloqueadoToUsuarios extends Migration
{
    public function up()
    {
        $this->forge->addColumn('usuarios', [
            'bloqueado' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'tipo'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('usuarios', 'bloqueado');
    }
}
