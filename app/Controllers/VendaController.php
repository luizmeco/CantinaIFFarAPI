<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class VendaController extends BaseController
{
    // Visualizar métricas de vendas
    public function index()
    {
        $this->verificarLogin();

        $db = \Config\Database::connect();

        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim = $this->request->getGet('data_fim');
        $perPage = $this->request->getGet('per_page') ?? 25;
        $page = $this->request->getGet('page') ?? 1;

        // Padrão de 10 dias se não fornecido
        if (empty($dataInicio) || empty($dataFim)) {
            $dataFim = date('Y-m-d');
            $dataInicio = date('Y-m-d', strtotime('-9 days'));
        }

        // Consultar vendas agregadas por dia
        $builder = $db->table('pedidos');
        $builder->select("DATE(pedidos.created_at) as data_venda");
        $builder->select("SUM(pedidos_produtos.quantidade * pedidos_produtos.preco_unitario) as valor_total");
        $builder->join('pedidos_produtos', 'pedidos_produtos.id_pedido = pedidos.id', 'inner');
        $builder->where("pedidos.created_at >= ", $dataInicio . ' 00:00:00');
        $builder->where("pedidos.created_at <= ", $dataFim . ' 23:59:59');
        $builder->whereIn("pedidos.status", ['efetuado', 'feito']);
        $builder->groupBy("DATE(pedidos.created_at)");
        $builder->orderBy("DATE(pedidos.created_at)", "DESC");

        $vendasPorDia = $builder->get()->getResultArray();

        // Mapear vendas encontradas
        $vendasMap = [];
        foreach ($vendasPorDia as $venda) {
            $vendasMap[$venda['data_venda']] = (float)$venda['valor_total'];
        }

        // Preencher o período completo (dias com R$ 0,00)
        $period = new \DatePeriod(
            new \DateTime($dataInicio),
            new \DateInterval('P1D'),
            (new \DateTime($dataFim))->modify('+1 day')
        );

        $dadosCompletos = [];
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $dadosCompletos[] = [
                'data'  => $dateStr,
                'total' => $vendasMap[$dateStr] ?? 0.0
            ];
        }

        // Ordenar decrescente para a tabela
        usort($dadosCompletos, function ($a, $b) {
            return strcmp($b['data'], $a['data']);
        });

        // Paginação manual do array para a tabela
        $total = count($dadosCompletos);
        $offset = ($page - 1) * $perPage;
        $vendasTabela = array_slice($dadosCompletos, $offset, $perPage);

        $pager = \Config\Services::pager();

        // Preparar dados cronológicos para o gráfico (esq -> dir)
        $dadosCronologico = $dadosCompletos;
        usort($dadosCronologico, function ($a, $b) {
            return strcmp($a['data'], $b['data']);
        });

        $chartLabels = array_column($dadosCronologico, 'data');
        $chartData = array_column($dadosCronologico, 'total');

        return view('pages/admin/vendas/index', [
            'vendas'       => $vendasTabela,
            'pager'        => $pager,
            'page'         => $page,
            'perPage'      => $perPage,
            'total'        => $total,
            'dataInicio'   => $dataInicio,
            'dataFim'      => $dataFim,
            'chartLabels'  => json_encode($chartLabels),
            'chartData'    => json_encode($chartData),
        ]);
    }
}
