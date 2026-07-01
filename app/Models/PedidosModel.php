<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidosModel extends Model
{
    protected $table            = 'pedidos';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [ 'id', 'status', 'totem', 'created_at', 'updated_at' ];

    // Dates
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
}
