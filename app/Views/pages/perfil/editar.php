<?php $this->extend('templates/admin') ?>

<?php $this->section('title') ?>Meus Dados<?php $this->endSection() ?>

<?php $this->section('conteudo') ?>

<h1>Meus Dados</h1>
<p class="text-muted">Mantenha suas informações cadastrais sempre atualizadas.</p>

<div class="row mt-4">
    <div class="col-md-6">
        <?php if (session()->getFlashdata('sucesso')) : ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('sucesso') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')) : ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $erro) : ?>
                        <li><?= esc($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('meus-dados/salvar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= old('email', session()->get('usuario')['email']) ?>" required>
            </div>

            <div class="mb-3 mt-4">
                <div class="card border-info">
                    <div class="card-body">
                        <h6 class="card-title text-info-emphasis">Alterar Senha (Opcional)</h6>
                        <p class="card-text text-muted small">Preencha os campos abaixo apenas se você deseja alterar a sua senha de acesso.</p>
                        
                        <div class="mb-3">
                            <label for="senha" class="form-label">Nova Senha</label>
                            <input type="password" name="senha" id="senha" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="confirmar_senha" class="form-label">Confirmar Nova Senha</label>
                            <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Salvar Meus Dados</button>
            </div>
        </form>
    </div>
</div>

<?php $this->endSection() ?>
