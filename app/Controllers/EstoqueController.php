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
            return redirect()->back()->with('error', 'Por favor, preencha todos os campos obrigatórios.');
        }

        $this->estoqueModel->insert([
            'id_produto' => $idProduto,
            'quantidade' => (int)$quantidade,
            'tipo'       => $tipo,
            'fornecedor' => $fornecedor ?: null,
            'observacao' => $observacao ?: null,
        ]);

        return redirect()->back()->with('success', 'Movimentação de estoque registrada com sucesso.');
    }

    // Ajuste rápido de estoque (+1 / -1)
    public function ajusteRapido($idProduto, $tipo)
    {
        $this->verificarLogin();

        if (!in_array($tipo, ['entrada', 'saida'])) {
            return redirect()->back()->with('error', 'Tipo de movimentação inválido.');
        }

        $produto = $this->produtoModel->find($idProduto);
        if (!$produto) {
            return redirect()->back()->with('error', 'Produto não encontrado.');
        }

        $this->estoqueModel->insert([
            'id_produto' => $idProduto,
            'quantidade' => 1,
            'tipo'       => $tipo,
            'observacao' => 'Ajuste rápido (' . ($tipo === 'entrada' ? '+1' : '-1') . ')',
        ]);

        $mensagem = $tipo === 'entrada' ? 'Estoque aumentado com sucesso (+1).' : 'Estoque diminuído com sucesso (-1).';
        return redirect()->back()->with('success', $mensagem);
    }

    // Visualizar histórico de movimentações de estoque do produto
    public function historico(int $idProduto)
    {
        $this->verificarLogin();

        $produto = $this->produtoModel->find($idProduto);
        if (!$produto) {
            return redirect()->to('admin/estoque')->with('error', 'Produto não encontrado.');
        }

        $db = \Config\Database::connect();

        // 1. Obter métricas rápidas do produto
        $sqlEstoqueEntrada = "SELECT COALESCE(SUM(quantidade), 0) AS total FROM estoques WHERE id_produto = :id: AND tipo = 'entrada'";
        $sqlEstoqueSaida = "SELECT COALESCE(SUM(quantidade), 0) AS total FROM estoques WHERE id_produto = :id: AND tipo = 'saida'";
        $sqlPedidos = "SELECT COALESCE(SUM(pedidos_produtos.quantidade), 0) AS total FROM pedidos_produtos INNER JOIN pedidos ON pedidos.id = pedidos_produtos.id_pedido WHERE pedidos_produtos.id_produto = :id: AND (pedidos.status = 'efetuado' OR pedidos.status = 'feito') AND pedidos.deleted_at IS NULL AND pedidos_produtos.deleted_at IS NULL";

        $totalEntradas = (int)$db->query($sqlEstoqueEntrada, ['id' => $idProduto])->getRow()->total;
        $totalSaidas = (int)$db->query($sqlEstoqueSaida, ['id' => $idProduto])->getRow()->total;
        $totalVendas = (int)$db->query($sqlPedidos, ['id' => $idProduto])->getRow()->total;
        $quantidadeRestante = $totalEntradas - $totalSaidas - $totalVendas;

        // 2. Filtros e paginação
        $tipoFiltro = $this->request->getGet('tipo'); // '', 'entrada', 'saida_manual', 'venda'
        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim = $this->request->getGet('data_fim');
        $perPage = (int)($this->request->getGet('per_page') ?? 10);
        $page = (int)($this->request->getGet('page') ?? 1);
        $offset = ($page - 1) * $perPage;

        // Construir query do histórico
        $queryEstoque = "SELECT 'estoque' AS origem, tipo, quantidade, fornecedor, observacao, created_at FROM estoques WHERE id_produto = :id_produto:";
        $queryVenda = "SELECT 'venda' AS origem, 'saida' AS tipo, pedidos_produtos.quantidade, NULL AS fornecedor, CONCAT('Venda - Pedido #', pedidos.id) AS observacao, pedidos.created_at FROM pedidos_produtos INNER JOIN pedidos ON pedidos.id = pedidos_produtos.id_pedido WHERE pedidos_produtos.id_produto = :id_produto: AND (pedidos.status = 'efetuado' OR pedidos.status = 'feito') AND pedidos.deleted_at IS NULL AND pedidos_produtos.deleted_at IS NULL";

        $params = ['id_produto' => $idProduto];
        
        if (!empty($dataInicio)) {
            $queryEstoque .= " AND created_at >= :data_inicio_est:";
            $queryVenda .= " AND pedidos.created_at >= :data_inicio_ven:";
            $params['data_inicio_est'] = $dataInicio . ' 00:00:00';
            $params['data_inicio_ven'] = $dataInicio . ' 00:00:00';
        }
        if (!empty($dataFim)) {
            $queryEstoque .= " AND created_at <= :data_fim_est:";
            $queryVenda .= " AND pedidos.created_at <= :data_fim_ven:";
            $params['data_fim_est'] = $dataFim . ' 23:59:59';
            $params['data_fim_ven'] = $dataFim . ' 23:59:59';
        }

        $queries = [];
        if (empty($tipoFiltro) || $tipoFiltro === 'entrada' || $tipoFiltro === 'saida_manual') {
            $subQueryEstoque = $queryEstoque;
            if ($tipoFiltro === 'entrada') {
                $subQueryEstoque .= " AND tipo = 'entrada'";
            } elseif ($tipoFiltro === 'saida_manual') {
                $subQueryEstoque .= " AND tipo = 'saida'";
            }
            $queries[] = $subQueryEstoque;
        }

        if (empty($tipoFiltro) || $tipoFiltro === 'venda') {
            $queries[] = $queryVenda;
        }

        $unionQuery = implode(" UNION ALL ", $queries);
        
        // Executar contagem
        $countQuery = "SELECT COUNT(*) AS total FROM ($unionQuery) AS temp";
        $totalResult = $db->query($countQuery, $params)->getRowArray();
        $total = (int)($totalResult['total'] ?? 0);

        // Executar busca com limites
        $dataQuery = "$unionQuery ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
        $historico = $db->query($dataQuery, $params)->getResultArray();

        $pager = \Config\Services::pager();

        return view('pages/admin/estoque/historico', [
            'produto'             => $produto,
            'historico'           => $historico,
            'totalEntradas'       => $totalEntradas,
            'totalSaidas'         => $totalSaidas,
            'totalVendas'         => $totalVendas,
            'quantidadeRestante'  => $quantidadeRestante,
            'tipo'                => $tipoFiltro,
            'dataInicio'          => $dataInicio,
            'dataFim'             => $dataFim,
            'perPage'             => $perPage,
            'page'                => $page,
            'total'               => $total,
            'pager'               => $pager
        ]);
    }
}
