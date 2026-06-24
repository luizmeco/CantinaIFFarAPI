<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table         = 'usuarios';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['email', 'senha_hash', 'tipo', 'bloqueado', 'reset_token', 'reset_token_date'];
    protected $useTimestamps = true;
}