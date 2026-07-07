<?php $this->extend('templates/admin') ?>

<?php $this->section('title') ?>Histórico de Estoque - <?= esc($produto['nome']) ?><?php $this->endSection() ?>

<?php $this->section('conteudo') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?= site_url('admin/estoque') ?>" class="text-decoration-none text-muted">Estoque</a></li>
                <li class="breadcrumb-item active" aria-current="page">Histórico</li>
            </ol>
        </nav>
        <h1 class="mb-0 fw-bold text-dark">📋 Histórico de Estoque</h1>
        <p class="text-muted mb-0">Detalhamento de movimentações do produto: <strong class="text-primary"><?= esc($produto['nome']) ?></strong></p>
    </div>
    
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalLancarEstoque">
            ➕ Novo Lançamento
        </button>
        <a href="<?= site_url('admin/estoque') ?>" class="btn btn-secondary">
            ⬅ Voltar
        </a>
    </div>
</div>

<!-- Grid de Métricas (KPI Cards) -->
<div class="row g-3 mb-4">
    <!-- Card Saldo -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #7f00ff 0%, #e100ff 100%); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1 fw-semibold text-uppercase small">Saldo Atual</h6>
                        <h3 class="mb-0 fw-bold"><?= $quantidadeRestante ?></h3>
                    </div>
                    <div style="font-size: 2rem; opacity: 0.8;">📦</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card Entradas -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1 fw-semibold text-uppercase small">Entradas (Total)</h6>
                        <h3 class="mb-0 fw-bold"><?= $totalEntradas ?></h3>
                    </div>
                    <div style="font-size: 2rem; opacity: 0.8;">📥</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card Saídas Manuais -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #f12711 0%, #f5af19 100%); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1 fw-semibold text-uppercase small">Saídas Manuais</h6>
                        <h3 class="mb-0 fw-bold"><?= $totalSaidas ?></h3>
                    </div>
                    <div style="font-size: 2rem; opacity: 0.8;">📤</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Card Vendas -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1 fw-semibold text-uppercase small">Vendas Efetuadas</h6>
                        <h3 class="mb-0 fw-bold"><?= $totalVendas ?></h3>
                    </div>
                    <div style="font-size: 2rem; opacity: 0.8;">🛒</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros de Pesquisa -->
<form id="filtroForm" method="get" action="<?= site_url('admin/estoque/historico/' . $produto['id']) ?>" class="bg-light p-3 rounded mb-4 border">
    <div class="row align-items-end g-3">
        <!-- Tipo de movimentação -->
        <div class="col-md-3">
            <label for="tipo" class="form-label fw-semibold">Filtrar por Tipo:</label>
            <select name="tipo" id="tipo" class="form-select" onchange="this.form.submit()">
                <option value="">Todas as movimentações</option>
                <option value="entrada" <?= $tipo == 'entrada' ? 'selected' : '' ?>>Entradas</option>
                <option value="saida_manual" <?= $tipo == 'saida_manual' ? 'selected' : '' ?>>Saídas Manuais</option>
                <option value="venda" <?= $tipo == 'venda' ? 'selected' : '' ?>>Vendas</option>
            </select>
        </div>

        <!-- Intervalo de datas -->
        <div class="col-md-5">
            <label class="form-label fw-semibold">Intervalo de datas:</label>
            <div class="input-group">
                <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?= esc($dataInicio ?? '') ?>">
                <span class="input-group-text">a</span>
                <input type="date" name="data_fim" id="data_fim" class="form-control" value="<?= esc($dataFim ?? '') ?>">
            </div>
        </div>

        <!-- Ações e paginação de registros por página -->
        <div class="col-md-4 d-flex gap-2">
            <select name="per_page" class="form-select w-auto" onchange="this.form.submit()">
                <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10 por pág.</option>
                <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25 por pág.</option>
                <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50 por pág.</option>
            </select>
            <button type="submit" class="btn btn-primary flex-fill">Filtrar</button>
            <?php if (!empty($tipo) || !empty($dataInicio) || !empty($dataFim)): ?>
                <a href="<?= site_url('admin/estoque/historico/' . $produto['id']) ?>" class="btn btn-secondary">Limpar</a>
            <?php endif; ?>
        </div>
    </div>
</form>

<!-- Tabela de Movimentações -->
<div class="card shadow-sm border mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold text-dark">📋 Lista de Movimentações</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 bg-white">
            <thead>
                <tr class="table-light">
                    <th>Data/Hora</th>
                    <th class="text-center">Tipo</th>
                    <th class="text-end">Quantidade</th>
                    <th>Fornecedor</th>
                    <th>Observação / Detalhes</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($historico)): ?>
                    <?php foreach ($historico as $item): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>
                            <td class="text-center">
                                <?php if ($item['origem'] === 'venda'): ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2 rounded-pill">
                                        🛒 Venda (Saída)
                                    </span>
                                <?php elseif ($item['tipo'] === 'entrada'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">
                                        📥 Entrada
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 rounded-pill">
                                        📤 Saída Manual
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold <?= ($item['tipo'] === 'entrada' ? 'text-success' : 'text-danger') ?>">
                                <?= ($item['tipo'] === 'entrada' ? '+' : '-') ?><?= esc($item['quantidade']) ?>
                            </td>
                            <td><?= esc($item['fornecedor'] ?: '-') ?></td>
                            <td><?= esc($item['observacao'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Nenhuma movimentação de estoque encontrada para este produto com os filtros selecionados.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Paginação -->
    <?php if ($total > $perPage): ?>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
            <span class="text-muted small">
                Pág. <?= $page ?> de <?= ceil($total / $perPage) ?> (total de <?= $total ?> registros)
            </span>
            <?= $pager->makeLinks($page, $perPage, $total, 'template_pager') ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Lançar Estoque -->
<div class="modal fade" id="modalLancarEstoque" tabindex="-1" aria-labelledby="modalLancarEstoqueLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title fw-bold" id="modalLancarEstoqueLabel">📦 Lançar Movimentação de Estoque</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/estoque/registrar') ?>" method="post">
                <input type="hidden" name="id_produto" value="<?= $produto['id'] ?>">
                
                <div class="modal-body p-4">
                    <!-- Produto (Visual apenas) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Produto</label>
                        <input type="text" class="form-control" style="border-radius: 10px;" value="<?= esc($produto['nome']) ?>" disabled>
                    </div>

                    <!-- Tipo de Movimentação -->
                    <div class="mb-3">
                        <label for="tipo_mov" class="form-label fw-semibold text-muted">Tipo de Movimentação <span class="text-danger">*</span></label>
                        <select name="tipo" id="tipo_mov" class="form-select" style="border-radius: 10px;" required>
                            <option value="entrada">Entrada (Aumentar estoque)</option>
                            <option value="saida">Saída (Diminuir estoque)</option>
                        </select>
                    </div>

                    <!-- Quantidade -->
                    <div class="mb-3">
                        <label for="qtd" class="form-label fw-semibold text-muted">Quantidade <span class="text-danger">*</span></label>
                        <input type="number" name="quantidade" id="qtd" class="form-control" min="1" placeholder="Digite a quantidade..." style="border-radius: 10px;" required>
                    </div>

                    <!-- Fornecedor -->
                    <div class="mb-3">
                        <label for="forn" class="form-label fw-semibold text-muted">Fornecedor (Opcional)</label>
                        <input type="text" name="fornecedor" id="forn" class="form-control" placeholder="Nome do fornecedor" style="border-radius: 10px;">
                    </div>

                    <!-- Observação -->
                    <div class="mb-3">
                        <label for="obs" class="form-label fw-semibold text-muted">Observação (Opcional)</label>
                        <textarea name="observacao" id="obs" class="form-control" rows="2" placeholder="Ex: Carga inicial, ajuste de estoque, etc." style="border-radius: 10px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary px-4" style="border-radius: 10px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px;">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->endSection() ?>
