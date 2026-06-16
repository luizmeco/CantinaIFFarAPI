<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\PedidosProdutosModel;
use App\Models\PedidosModel;
use App\Models\ProdutoModel;


class ApiController extends BaseController
{
    use ResponseTrait;

    public function api_status()
    {
        return $this->respond(['status' => 'API is running'], 200);
    }

    public function get_produtos()
    {
        $apiKey = $this->request->getHeaderLine('apiKey');

        if($apiKey !== env('API_KEY')) {
            return $this->respond(['error' => 'Unauthorized'], ResponseInterface::HTTP_UNAUTHORIZED);
        }
        $produtosModel = new ProdutoModel();
        $produtos = $produtosModel->findAll();
        $pedidosProdutosModel = new PedidosProdutosModel();
        $pedidosProdutos = $pedidosProdutosModel->select('pedidos_produtos.*, produtos.nome')
                                                ->join('produtos', 'produtos.id = pedidos_produtos.id_produto', 'inner')
                                                ->findAll();

        return $this->respond([
            'produtos'         => $produtos,
            'pedidos_produtos' => $pedidosProdutos
        ], 200);
    }

    public function checkout()
    {
        $apiKey = $this->request->getHeaderLine('apiKey');
        //vamos modificar posteriormente para que cada "cliente" tenha uma chave unica

        if (!$apiKey) {
            return $this->failUnauthorized('API Key não informada.');
        }

        if($apiKey!=env('API_KEY')){
            return $this->failUnauthorized('API Key inválida.');
        }

        $dados = $this->request->getJSON(true);

        //verifica se os dados do pedido foram informados
        if (!$dados) {
            return $this->failValidationErrors('JSON inválido.');
        }

        if (!isset($dados['produtos']) || empty($dados['produtos'])) {
            return $this->failValidationErrors('O pedido precisa ter pelo menos um produto.');
        }


        $pedidoModel = new PedidosModel();
        
        $pedidoProdutoModel = new PedidosProdutosModel();


        $db = \Config\Database::connect();

        $db->transStart();
        
        //primeiro cadastramos o pedido para termos o ID
        $idPedido = $pedidoModel->insert([
            'status' => $dados['status'] ?? 'novo'
        ]);

				//para o pedido enviado, verificamos todos os produtos enviados
        foreach ($dados['produtos'] as $produto) {
            $pedidoProdutoModel->insert([
                'id_pedido' => $idPedido,
                'id_produto' => $produto['id_produto'],
                'quantidade' => $produto['quantidade'],
                'preco_unitario' => $produto['preco_unitario']
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() == false) {
            return $this->failServerError('Erro ao cadastrar pedido.');
        }

        return $this->respondCreated([
            'status'    => true,
            'message'   => 'Pedido cadastrado com sucesso.',
            'id_pedido' => $idPedido
        ]);
    }

    public function adicionarAoCarrinho()
    {
        $apiKey = $this->request->getHeaderLine('apiKey');

        if (!$apiKey || $apiKey != env('API_KEY')) {
            return $this->failUnauthorized('API Key inválida ou não informada.');
        }

        $dados = $this->request->getJSON(true);

        // Verifica se os dados necessários do produto foram informados
        if (!$dados || !isset($dados['id_produto'])) {
            return $this->failValidationErrors('Dados do produto incompletos. Informe id_produto.');
        }

        $pedidoModel = new PedidosModel();
        $pedidoProdutoModel = new PedidosProdutosModel();
        $produtoModel = new ProdutoModel();

        $produto = $produtoModel->find($dados['id_produto']);
        if (!$produto) {
            return $this->failNotFound('Produto não encontrado.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $idPedido = $dados['id_pedido'] ?? null;

        // Verifica se já existe um pedido
        if ($idPedido) {
            if (!$pedidoModel->find($idPedido)) {
                return $this->failNotFound('Pedido informado não encontrado.');
            }
        } else {
            // Cria um novo pedido caso não tenha sido enviado um id_pedido
            $idPedido = $pedidoModel->insert([
                'status' => 'novo'
            ]);
        }

        // Verifica se o produto já existe no pedido
        $itemExistente = $pedidoProdutoModel->where('id_pedido', $idPedido)
                                            ->where('id_produto', $dados['id_produto'])
                                            ->first();

        if ($itemExistente) {
            $pedidoProdutoModel->update($itemExistente['id'], [
                'quantidade' => $itemExistente['quantidade'] + 1
            ]);
        } else {
            // Adiciona o produto na tabela pedidos_produtos
            $pedidoProdutoModel->insert([
                'id_pedido'      => $idPedido,
                'id_produto'     => $dados['id_produto'],
                'quantidade'     => 1,
                'preco_unitario' => $produto['preco']
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() == false) {
            return $this->failServerError('Erro ao processar a requisição e adicionar produto ao pedido.');
        }

        return $this->respondCreated([
            'status'    => true,
            'message'   => 'Produto adicionado com sucesso.',
            'id_pedido' => $idPedido
        ]);
    }

    public function removerItemPedido()
    {
        $apiKey = $this->request->getHeaderLine('apiKey');

        if (!$apiKey || $apiKey != env('API_KEY')) {
            return $this->failUnauthorized('API Key inválida ou não informada.');
        }

        $dados = $this->request->getJSON(true);

        if (!$dados || !isset($dados['id_pedido']) || !isset($dados['id_produto'])) {
            return $this->failValidationErrors('Dados incompletos. Informe id_pedido e id_produto.');
        }

        $pedidoProdutoModel = new PedidosProdutosModel();

        $itemExistente = $pedidoProdutoModel->where('id_pedido', $dados['id_pedido'])
                                            ->where('id_produto', $dados['id_produto'])
                                            ->first();

        if (!$itemExistente) {
            return $this->failNotFound('Produto não encontrado no pedido informado.');
        }

        $pedidoProdutoModel->delete($itemExistente['id']);

        return $this->respondDeleted([
            'status'  => true,
            'message' => 'Produto removido do pedido com sucesso.'
        ]);
    }

    public function atualizarItemPedido()
    {
        $apiKey = $this->request->getHeaderLine('apiKey');

        if (!$apiKey || $apiKey != env('API_KEY')) {
            return $this->failUnauthorized('API Key inválida ou não informada.');
        }

        $dados = $this->request->getJSON(true);

        if (!$dados || !isset($dados['id_pedido']) || !isset($dados['id_produto']) || !isset($dados['quantidade'])) {
            return $this->failValidationErrors('Dados incompletos. Informe id_pedido, id_produto e quantidade.');
        }

        $pedidoProdutoModel = new PedidosProdutosModel();

        $itemExistente = $pedidoProdutoModel->where('id_pedido', $dados['id_pedido'])
                                            ->where('id_produto', $dados['id_produto'])
                                            ->first();

        if (!$itemExistente) {
            return $this->failNotFound('Produto não encontrado no pedido informado.');
        }

        $pedidoProdutoModel->update($itemExistente['id'], [
            'quantidade' => (int) $dados['quantidade']
        ]);

        return $this->respond([
            'status'  => true,
            'message' => 'Quantidade do produto atualizada com sucesso.'
        ], 200);
    }

    public function limparPedido()
    {
        $apiKey = $this->request->getHeaderLine('apiKey');

        if (!$apiKey || $apiKey != env('API_KEY')) {
            return $this->failUnauthorized('API Key inválida ou não informada.');
        }

        $dados = $this->request->getJSON(true);

        if (!$dados || !isset($dados['id_pedido'])) {
            return $this->failValidationErrors('Dados incompletos. Informe id_pedido.');
        }

        $pedidoModel = new PedidosModel();
        $pedidoProdutoModel = new PedidosProdutosModel();

        $pedido = $pedidoModel->find($dados['id_pedido']);
        if (!$pedido) {
            return $this->failNotFound('Pedido não encontrado.');
        }

        // Remove todos os itens (carrinho) vinculados ao pedido
        $pedidoProdutoModel->where('id_pedido', $dados['id_pedido'])->delete();

        // Remove o próprio pedido principal da tabela pedidos
        $pedidoModel->delete($dados['id_pedido']);

        return $this->respondDeleted([
            'status'  => true,
            'message' => 'Pedido finalizado e limpo do banco de dados com sucesso.'
        ]);
    }
}
