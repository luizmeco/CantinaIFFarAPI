<?php $this->extend('templates/admin') ?>

<?php $this->section('title') ?>Métricas de Estoque<?php $this->endSection() ?>

<?php $this->section('conteudo') ?>

<h1 class="mb-4">Métricas de Estoque</h1>

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
