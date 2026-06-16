<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProdutoModel;
use App\Models\EstoqueModel;

class EstoqueController extends BaseController
{
    protected ProdutoModel $produtoModel;
    protected EstoqueModel $estoqueModel;

    public function __construct()
    {
        $this->produtoModel = new ProdutoModel();
        $this->estoqueModel = new EstoqueModel();
    }

    public function index()
    {
        //
    }
}
