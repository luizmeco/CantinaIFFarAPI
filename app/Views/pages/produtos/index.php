<?php $this->extend('templates/admin') ?>

<?php $this->section('title') ?>Produtos<?php $this->endSection() ?>

<?php $this->section('conteudo') ?>

<h1>Produtos</h1>

<div class="mb-3">
    <a href='<?= site_url('admin/produtos/novo') ?>' class="btn btn-primary">Cadastrar novo produto</a>
</div>

<?php if (!empty($produtos)) : ?>

    <!-- filtros -->
    <form method='get' action='<?= site_url('produtos') ?>' class="mb-5">
        <div class="row">
            <div class="col-md-4">
                <input type='text' name='busca' value='<?= esc($busca ?? '') ?>' placeholder='Buscar produto por nome...' class="form-control">
            </div>
            <div class="col-md-3">
                <select name="preco" class="form-select">
                    <option value="">Todos</option>
                    <option value="baixo">Abaixo de R$ 5</option>
                    <option value="medio">Entre R$ 5 e R$ 10</option>
                    <option value="alto">Acima de R$ 10</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="categoria" class="form-select">
                    <option value="">Todas as categorias</option>
                    <option value="Lanche" <?= !empty($categoria) && $categoria == 'Lanche' ? 'selected' : '' ?>>Lanche</option>
                    <option value="Bebida" <?= !empty($categoria) && $categoria == 'Bebida' ? 'selected' : '' ?>>Bebida</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type='submit' class="btn btn-primary">Buscar</button>
                <?php if ($busca): ?>
                    <a href='<?= site_url('produtos') ?>' class="btn btn-secondary ms-2">Limpar</a>
                <?php endif ?>
            </div>
    </form>

    <!-- tabela -->

    <div class="table-responsive mt-1">
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Foto</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                        <?php foreach ($produtos as $produto) : ?>
                            <tr>
                                <td><?= esc($produto['id']) ?></td>
                                <td><?= esc($produto['nome']) ?></td>
                                <td><?= esc($produto['categoria']) ?></td>
                                <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                                <td>
                                    <?php if (!empty($produto['foto'])): ?>
                                        <a href="<?= base_url('uploads/produtos/' . esc($produto['foto'])) ?>" target="_blank">
                                            <img src='<?= base_url('uploads/produtos/' . esc($produto['foto'])) ?>'
                                            alt='<?= esc($produto['nome']) ?>'
                                            style='width:60px; height:60px; object-fit: cover; border-radius: 5px;'>
                                    </a>
                                    <?php else: ?>
                                        <span >Sem foto</span>
                                    <?php endif ?>
                                </td>

                                <td>
                                    <a href='<?= site_url('admin/produtos/editar/' . $produto['id']) ?>' class="btn btn-sm btn-warning me-2">Editar</a>
                                    <a href='<?= site_url('admin/produtos/excluir/' . $produto['id']) ?>'
                                        onclick='return confirm("Excluir?")' class="btn btn-sm btn-danger">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>


            <!-- PAGINAÇÃO -->
            <p>
                Página <?= $pager->getCurrentPage() ?> de <?= $pager->getPageCount() ?> - mostrando <?= count($produtos) ?>
                de <?= $pager->getTotal() ?> registros
            </p>

            <?= $pager->links('default', 'template_pager') ?>

        <?php else : ?>

            <div class="alert alert-warning">
                <p>Nenhum produto cadastrado.</p>
            </div>

        <?php endif; ?>

<?php $this->endSection() ?>