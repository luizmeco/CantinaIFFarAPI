<?php $this->extend('templates/admin') ?>

<?php $this->section('title') ?>Métricas de Estoque<?php $this->endSection() ?>

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
    <h1 class="mb-0">Métricas de Estoque</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalLancarEstoque">
        ➕ Lançar Estoque
    </button>
</div>

<!-- Filtros superiores -->
<form id="filtroForm" method="get" action="<?= site_url('admin/estoque') ?>" class="bg-light p-3 rounded mb-4 border">
    <div class="row align-items-end g-3">
        <!-- Categoria -->
        <div class="col-md-3">
            <label for="categoria" class="form-label fw-semibold">Categorias:</label>
            <select name="categoria" id="categoria" class="form-select" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="Lanche" <?= $categoria == 'Lanche' ? 'selected' : '' ?>>Lanches</option>
                <option value="Bebida" <?= $categoria == 'Bebida' ? 'selected' : '' ?>>Bebidas</option>
                <option value="Acompanhamento" <?= $categoria == 'Acompanhamento' ? 'selected' : '' ?>>Acompanhamentos</option>
                <option value="Sobremesa" <?= $categoria == 'Sobremesa' ? 'selected' : '' ?>>Sobremesas</option>
            </select>
        </div>

        <!-- Intervalo de datas -->
        <div class="col-md-5">
            <label class="form-label fw-semibold">Intervalo de datas:</label>
            <div class="input-group">
                <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?= esc($dataInicio ?? '') ?>" placeholder="Data Início">
                <span class="input-group-text">a</span>
                <input type="date" name="data_fim" id="data_fim" class="form-control" value="<?= esc($dataFim ?? '') ?>" placeholder="Data Fim">
            </div>
        </div>

        <!-- Botões de atalho -->
        <div class="col-md-4 d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary flex-fill" onclick="setarPeriodo(7)">Últimos 7 dias</button>
            <button type="button" class="btn btn-outline-secondary flex-fill" onclick="limparPeriodo()">Desde sempre</button>
            <button type="submit" class="btn btn-primary" title="Atualizar">
                🔄
            </button>
        </div>
    </div>

    <!-- Filtros de paginação e busca -->
    <div class="row align-items-center mt-3 pt-3 border-top g-3">
        <div class="col-md-4 d-flex align-items-center gap-2">
            <span>Mostrando</span>
            <select name="per_page" class="form-select w-auto" onchange="this.form.submit()">
                <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
                <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
            </select>
            <span>registros por página.</span>
        </div>
        
        <div class="col-md-5 ms-auto">
            <div class="input-group">
                <span class="input-group-text bg-white">Filtrar:</span>
                <input type="text" name="busca" class="form-control" value="<?= esc($busca ?? '') ?>" placeholder="Digite o nome do produto...">
                <button class="btn btn-primary" type="submit">Buscar</button>
                <?php if (!empty($busca) || !empty($categoria) || !empty($dataInicio) || !empty($dataFim)): ?>
                    <a href="<?= site_url('admin/estoque') ?>" class="btn btn-secondary">Limpar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<!-- Tabela de Produtos/Estoque -->
<?php if (!empty($produtos)): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white">
            <thead>
                <tr class="table-light">
                    <th>Produto</th>
                    <th>Categoria</th>
                    <th class="text-center">Disponível</th>
                    <th class="text-end">Stock atual</th>
                    <th class="text-end">Quantidade</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 50px; height: 50px; border-radius: 8px; border: 1.5px solid #dee2e6; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                    <?php if (!empty($produto['foto'])): ?>
                                        <img src="<?= base_url('uploads/produtos/' . esc($produto['foto'])) ?>" alt="<?= esc($produto['nome']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <span style="font-size: 1.5rem;">🍔</span>
                                    <?php endif; ?>
                                </div>
                                <span class="fw-semibold text-primary"><?= esc($produto['nome']) ?></span>
                            </div>
                        </td>
                        <td><?= esc($produto['categoria'] ?: 'Sem categoria') ?></td>
                        <td class="text-center">
                            <?php if ($produto['disponivel']): ?>
                                <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-2">
                                    ✓ Disponível
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill px-3 py-2">
                                    ✗ Esgotado
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end fw-semibold text-muted"><?= esc($produto['stock_atual']) ?></td>
                        <td class="text-end fw-bold text-success-emphasis"><?= esc($produto['quantidade_restante']) ?></td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-2">
                                <a href="<?= site_url('admin/estoque/ajuste-rapido/' . $produto['id'] . '/entrada') ?>" class="btn btn-sm btn-success px-2 py-1 fw-bold" title="Aumentar estoque em 1 unidade">
                                    +1
                                </a>
                                <a href="<?= site_url('admin/estoque/ajuste-rapido/' . $produto['id'] . '/saida') ?>" class="btn btn-sm btn-danger px-2 py-1 fw-bold" title="Diminuir estoque em 1 unidade">
                                    -1
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <p class="text-muted mb-0">
            Mostrando <?= count($produtos) ?> de <?= $pager->getTotal() ?> registros.
        </p>
        <?= $pager->links('default', 'template_pager') ?>
    </div>

<?php else: ?>
    <div class="alert alert-warning">
        Nenhum produto correspondente aos filtros foi encontrado.
    </div>
<?php endif; ?>

<!-- Modal Lançar Estoque -->
<div class="modal fade" id="modalLancarEstoque" tabindex="-1" aria-labelledby="modalLancarEstoqueLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title fw-bold" id="modalLancarEstoqueLabel">📦 Lançar Movimentação de Estoque</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/estoque/registrar') ?>" method="post">
                <div class="modal-body p-4">
                    <!-- Produto -->
                    <div class="mb-3">
                        <label for="id_produto" class="form-label fw-semibold text-muted">Produto <span class="text-danger">*</span></label>
                        <select name="id_produto" id="id_produto" class="form-select" style="border-radius: 10px;" required>
                            <option value="">Selecione um produto...</option>
                            <?php foreach ($todosProdutos as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= esc($p['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
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

<?php $this->section('scripts') ?>
<script>
    function setarPeriodo(dias) {
        const fim = new Date();
        const inicio = new Date();
        inicio.setDate(fim.getDate() - (dias - 1));

        document.getElementById('data_fim').value = fim.toISOString().split('T')[0];
        document.getElementById('data_inicio').value = inicio.toISOString().split('T')[0];
        
        document.getElementById('filtroForm').submit();
    }

    function limparPeriodo() {
        document.getElementById('data_inicio').value = '';
        document.getElementById('data_fim').value = '';
        document.getElementById('filtroForm').submit();
    }
</script>
<?php $this->endSection() ?>
