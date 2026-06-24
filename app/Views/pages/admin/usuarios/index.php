<?php $this->extend('templates/admin') ?>

<?php $this->section('title') ?>Usuários<?php $this->endSection() ?>

<?php $this->section('conteudo') ?>

<h1>Usuários</h1>

<div class="mb-3">
    <a href='<?= site_url('admin/usuarios/novo') ?>' class="btn btn-primary">Cadastrar novo usuário</a>
</div>

<?php if (session()->getFlashdata('sucesso')) : ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('sucesso') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('erro')) : ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('erro') ?>
    </div>
<?php endif; ?>

<?php if (!empty($usuarios)) : ?>

    <!-- tabela -->
    <div class="table-responsive mt-3">
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>E-mail</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usr) : ?>
                    <tr>
                        <td><?= esc($usr['id']) ?></td>
                        <td><?= esc($usr['email']) ?></td>
                        <td>
                            <span class="badge <?= $usr['tipo'] === 'admin' ? 'bg-danger' : 'bg-secondary' ?>">
                                <?= esc($usr['tipo']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($usr['bloqueado'] == 1) : ?>
                                <span class="badge bg-warning text-dark">Bloqueado</span>
                            <?php else : ?>
                                <span class="badge bg-success">Ativo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href='<?= site_url('admin/usuarios/editar/' . $usr['id']) ?>' class="btn btn-sm btn-warning me-2">Editar</a>
                            
                            <?php if ($usr['id'] == session()->get('usuario')['id']) : ?>
                                <button class="btn btn-sm btn-outline-secondary" disabled title="Você não pode bloquear a si mesmo">Bloquear</button>
                            <?php elseif ($usr['bloqueado'] == 1) : ?>
                                <a href='<?= site_url('admin/usuarios/desbloquear/' . $usr['id']) ?>' 
                                   onclick='return confirm("Deseja desbloquear este usuário?")' class="btn btn-sm btn-success">Desbloquear</a>
                            <?php else : ?>
                                <a href='<?= site_url('admin/usuarios/bloquear/' . $usr['id']) ?>' 
                                   onclick='return confirm("Deseja bloquear este usuário?")' class="btn btn-sm btn-danger">Bloquear</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINAÇÃO -->
    <p class="mt-3">
        Página <?= $pager->getCurrentPage() ?> de <?= $pager->getPageCount() ?> - mostrando <?= count($usuarios) ?>
        de <?= $pager->getTotal() ?> registros
    </p>

    <?= $pager->links('default', 'template_pager') ?>

<?php else : ?>
    <div class="alert alert-warning">
        <p>Nenhum usuário cadastrado.</p>
    </div>
<?php endif; ?>

<?php $this->endSection() ?>
