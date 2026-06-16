<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidosProdutosModel extends Model
{
    protected $table            = 'pedidos_produtos';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [ 'id_pedido', 'id_produto', 'quantidade', 'preco_unitario', 'created_at', 'updated_at' ];

    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
}
