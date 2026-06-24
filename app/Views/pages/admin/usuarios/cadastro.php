<?php $this->extend('templates/admin') ?>

<?php $this->section('title') ?>Novo Usuário<?php $this->endSection() ?>

<?php $this->section('conteudo') ?>

<h1>Novo Usuário</h1>

<div class="row mt-4">
    <div class="col-md-6">
        <?php if (session()->getFlashdata('errors')) : ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $erro) : ?>
                        <li><?= esc($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('admin/usuarios/salvar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= old('email') ?>" required>
            </div>

            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" name="senha" id="senha" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo de Usuário</label>
                <select name="tipo" id="tipo" class="form-select" required>
                    <option value="usuario" <?= old('tipo') === 'usuario' ? 'selected' : '' ?>>Usuário Comum</option>
                    <option value="admin" <?= old('tipo') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="<?= site_url('admin/usuarios') ?>" class="btn btn-secondary ms-2">Voltar</a>
            </div>
        </form>
    </div>
</div>

<?php $this->endSection() ?>
