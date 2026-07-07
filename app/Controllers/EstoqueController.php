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

    // Visualizar métricas de estoque
    public function metricas()
    {
        $this->verificarLogin();

        $db = \Config\Database::connect();

        $categoria = $this->request->getGet('categoria');
        $busca = $this->request->getGet('busca');
        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim = $this->request->getGet('data_fim');
        $perPage = $this->request->getGet('per_page') ?? 10;

        // Subqueries base
        $sqlEstoqueEntrada = "SELECT COALESCE(SUM(quantidade), 0) FROM estoques WHERE estoques.id_produto = produtos.id AND estoques.tipo = 'entrada'";
        $sqlEstoqueSaida = "SELECT COALESCE(SUM(quantidade), 0) FROM estoques WHERE estoques.id_produto = produtos.id AND estoques.tipo = 'saida'";
        $sqlPedidos = "SELECT COALESCE(SUM(pedidos_produtos.quantidade), 0) FROM pedidos_produtos INNER JOIN pedidos ON pedidos.id = pedidos_produtos.id_pedido WHERE pedidos_produtos.id_produto = produtos.id AND (pedidos.status = 'efetuado' OR pedidos.status = 'feito')";

        // Filtro por período se fornecido
        if (!empty($dataInicio) && !empty($dataFim)) {
            $escDataInicio = $db->escape($dataInicio . ' 00:00:00');
            $escDataFim = $db->escape($dataFim . ' 23:59:59');

            $sqlEstoqueEntrada .= " AND estoques.created_at >= $escDataInicio AND estoques.created_at <= $escDataFim";
            $sqlEstoqueSaida .= " AND estoques.created_at >= $escDataInicio AND estoques.created_at <= $escDataFim";
            $sqlPedidos .= " AND pedidos.created_at >= $escDataInicio AND pedidos.created_at <= $escDataFim";
        }

        // Aplicando selects customizados ao model
        $this->produtoModel->select('produtos.*');
        $this->produtoModel->select("($sqlEstoqueEntrada) AS stock_atual");
        $this->produtoModel->select("($sqlEstoqueSaida) AS total_saidas");
        $this->produtoModel->select("($sqlPedidos) AS total_vendidos");

        if (!empty($categoria)) {
            $this->produtoModel->where('produtos.categoria', $categoria);
        }

        if (!empty($busca)) {
            $this->produtoModel->like('produtos.nome', $busca);
        }

        $produtos = $this->produtoModel->paginate($perPage);

        // Processamento final das quantidades dinâmicas
        foreach ($produtos as &$prod) {
            $prod['quantidade_restante'] = (int)$prod['stock_atual'] - (int)$prod['total_saidas'] - (int)$prod['total_vendidos'];
            $prod['disponivel'] = $prod['quantidade_restante'] > 0;
        }

        $todosProdutos = $this->produtoModel->select('id, nome')->orderBy('nome', 'ASC')->findAll();

        return view('pages/admin/estoque/metricas', [
            'produtos'      => $produtos,
            'pager'         => $this->produtoModel->pager,
            'categoria'     => $categoria,
            'busca'         => $busca,
            'dataInicio'    => $dataInicio,
            'dataFim'       => $dataFim,
            'perPage'       => $perPage,
            'todosProdutos' => $todosProdutos
        ]);
    }

    // Registrar movimentação de estoque de um produto cadastrado no banco
    public function registrar()
    {
        $this->verificarLogin();

        $idProduto = $this->request->getPost('id_produto');
        $quantidade = $this->request->getPost('quantidade');
        $tipo = $this->request->getPost('tipo');
        $fornecedor = $this->request->getPost('fornecedor');
        $observacao = $this->request->getPost('observacao');

        if (!$idProduto || !$quantidade || !$tipo) {
            return redirect()->to('admin/estoque')->with('error', 'Por favor, preencha todos os campos obrigatórios.');
        }

        $this->estoqueModel->insert([
            'id_produto' => $idProduto,
            'quantidade' => (int)$quantidade,
            'tipo'       => $tipo,
            'fornecedor' => $fornecedor ?: null,
            'observacao' => $observacao ?: null,
        ]);

        return redirect()->to('admin/estoque')->with('success', 'Movimentação de estoque registrada com sucesso.');
    }

    // Ajuste rápido de estoque (+1 / -1)
    public function ajusteRapido($idProduto, $tipo)
    {
        $this->verificarLogin();

        if (!in_array($tipo, ['entrada', 'saida'])) {
            return redirect()->to('admin/estoque')->with('error', 'Tipo de movimentação inválido.');
        }

        $produto = $this->produtoModel->find($idProduto);
        if (!$produto) {
            return redirect()->to('admin/estoque')->with('error', 'Produto não encontrado.');
        }

        $this->estoqueModel->insert([
            'id_produto' => $idProduto,
            'quantidade' => 1,
            'tipo'       => $tipo,
            'observacao' => 'Ajuste rápido (' . ($tipo === 'entrada' ? '+1' : '-1') . ')',
        ]);

        $mensagem = $tipo === 'entrada' ? 'Estoque aumentado com sucesso (+1).' : 'Estoque diminuído com sucesso (-1).';
        return redirect()->to('admin/estoque')->with('success', $mensagem);
    }
}
