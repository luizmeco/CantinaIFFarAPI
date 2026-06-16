<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdutoModel extends Model
{
    protected $table         = 'produtos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nome', 'preco', 'foto', 'categoria'];

    protected $useTimestamps = true;

    protected $validationRules = [
        'nome'      => 'required|min_length[2]|max_length[100]',
        'preco'     => 'required|decimal|greater_than[0]',
        'categoria' => 'required|in_list[Lanche,Bebida]'
    ];

    // protected $validationMessages = [
    //     'nome' => [
    //         'required'   => 'O nome do produto e obrigatorio.',
    //         'min_length' => 'O nome deve ter pelo menos 2 caracteres.',
    //         'max_length' => 'O nome deve ter no maximo 100 caracteres.',
    //     ],
    //     'preco' => [
    //         'required'      => 'O preco e obrigatorio.',
    //         'decimal'       => 'Informe um preco valido (ex: 12.50).',
    //         'greater_than'  => 'O preco deve ser maior que zero.',
    //     ],
    //     'categoria' => [
    //         'required' => 'A categoria e obrigatoria.',
    //         'in_list'  => 'Categoria deve ser Lanche ou Bebida.',
    //     ],
    // ];
}
