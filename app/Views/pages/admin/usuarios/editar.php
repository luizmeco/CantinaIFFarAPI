<?php $this->extend('templates/admin') ?>

<?php $this->section('title') ?>Editar Usuário<?php $this->endSection() ?>

<?php $this->section('conteudo') ?>

<h1>Editar Usuário</h1>

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

        <form action="<?= site_url('admin/usuarios/atualizar/' . $usuario['id']) ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= old('email', $usuario['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo de Usuário</label>
                <select name="tipo" id="tipo" class="form-select" required <?= $usuario['id'] == session()->get('usuario')['id'] ? 'disabled' : '' ?>>
                    <option value="usuario" <?= old('tipo', $usuario['tipo']) === 'usuario' ? 'selected' : '' ?>>Usuário Comum</option>
                    <option value="admin" <?= old('tipo', $usuario['tipo']) === 'admin' ? 'selected' : '' ?>>Administrador</option>
                </select>
                <?php if ($usuario['id'] == session()->get('usuario')['id']) : ?>
                    <input type="hidden" name="tipo" value="admin">
                    <div class="form-text text-muted">Você não pode alterar sua própria função.</div>
                <?php endif; ?>
            </div>

            <div class="mb-3 mt-4">
                <div class="card border-warning">
                    <div class="card-body">
                        <h6 class="card-title text-warning-emphasis">Alterar Senha (Opcional)</h6>
                        <p class="card-text text-muted small">Deixe o campo abaixo em branco caso não queira alterar a senha deste usuário.</p>
                        <label for="senha" class="form-label">Nova Senha</label>
                        <input type="password" name="senha" id="senha" class="form-control">
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                <a href="<?= site_url('admin/usuarios') ?>" class="btn btn-secondary ms-2">Voltar</a>
            </div>
        </form>
    </div>
</div>

<?php $this->endSection() ?>
