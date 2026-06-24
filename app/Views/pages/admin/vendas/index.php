<?php $this->extend('templates/admin') ?>

<?php $this->section('title') ?>Métricas de Vendas<?php $this->endSection() ?>

<?php $this->section('conteudo') ?>

<h1 class="mb-4">Métricas de Vendas</h1>

<!-- Filtros superiores -->
<form id="filtroForm" method="get" action="<?= site_url('admin/vendas') ?>" class="bg-light p-3 rounded mb-4 border">
    <div class="row align-items-end g-3">
        <!-- Intervalo de datas -->
        <div class="col-md-5">
            <label class="form-label fw-semibold">Intervalo de datas:</label>
            <div class="input-group">
                <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?= esc($dataInicio ?? '') ?>" placeholder="Data Início">
                <span class="input-group-text">a</span>
                <input type="date" name="data_fim" id="data_fim" class="form-control" value="<?= esc($dataFim ?? '') ?>" placeholder="Data Fim">
            </div>
        </div>

        <div class="col-md-4">
            <span class="text-muted d-block mb-1 small fw-semibold">Período ativo:</span>
            <span class="badge bg-primary px-3 py-2 fs-6">
                📅 <?= date('d/m/Y', strtotime($dataInicio)) ?> a <?= date('d/m/Y', strtotime($dataFim)) ?>
            </span>
        </div>

        <!-- Botões de atalho -->
        <div class="col-md-3 d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary flex-fill" onclick="setarPeriodo(7)">Últimos 7 dias</button>
            <button type="submit" class="btn btn-primary" title="Atualizar">
                🔄 Atualizar
            </button>
        </div>
    </div>
</form>

<div class="row mt-4">
    <!-- Tabela de Vendas Diárias -->
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm border">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">Faturamento Diário</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead>
                            <tr class="table-light">
                                <th>Data</th>
                                <th class="text-end">Valor total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($vendas)): ?>
                                <?php foreach ($vendas as $venda): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-muted">
                                                <?= date('d/m/Y', strtotime($venda['data'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            R$ <?= number_format($venda['total'], 2, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">
                                        Nenhuma venda registrada no período.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Paginação do Pager -->
            <?php if ($total > $perPage): ?>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted small">
                        Pág. <?= $page ?> de <?= ceil($total / $perPage) ?> (total <?= $total ?> dias)
                    </span>
                    <?= $pager->makeLinks($page, $perPage, $total, 'template_pager') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Gráfico de Vendas -->
    <div class="col-md-7 mb-4">
        <div class="card shadow-sm border h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">Gráfico de vendas dos últimos 10 dias</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 350px;">
                <div class="w-100 h-100">
                    <canvas id="salesChart" style="max-height: 380px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<!-- Inclusão do Chart.js via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function setarPeriodo(dias) {
        const fim = new Date();
        const inicio = new Date();
        inicio.setDate(fim.getDate() - (dias - 1));

        document.getElementById('data_fim').value = fim.toISOString().split('T')[0];
        document.getElementById('data_inicio').value = inicio.toISOString().split('T')[0];
        
        document.getElementById('filtroForm').submit();
    }

    document.addEventListener("DOMContentLoaded", function() {
        const labelsRaw = <?= $chartLabels ?>;
        const dataRaw = <?= $chartData ?>;

        // Formatar datas para d/m no gráfico
        const labelsFormatadas = labelsRaw.map(dateStr => {
            const parts = dateStr.split('-');
            return `${parts[2]}/${parts[1]}`;
        });

        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labelsFormatadas,
                datasets: [{
                    label: 'Faturamento (R$)',
                    data: dataRaw,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#764ba2',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 7,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return 'R$ ' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
<?php $this->endSection() ?>
