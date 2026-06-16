<?php $this->extend('templates/auth') ?>

<?php $this->section('title') ?>Redefinir Senha<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="auth-header">
    <h2>Criar Nova Senha</h2>
    <p>Digite a sua nova senha</p>
</div>

<?php if(session()->getFlashData('erros')): ?>
<div class="alert alert-danger">
    <?= session()->getFlashData('erros') ?>
</div>
<?php endif; ?>

<form action="<?= site_url('salvar_nova_senha') ?>" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="token" value="<?= esc($token) ?>">
    <input type="password" name="senha" class="form-control" placeholder="Nova Senha" required />
    <input type="password" name="confirmar_senha" class="form-control" placeholder="Confirmar Senha" required />
    <button type="submit" class="btn btn-primary">Salvar Nova Senha</button>
</form>

<div class="auth-links">
    <a href="<?= site_url('login') ?>">Voltar ao login</a>
</div>
<?php $this->endSection() ?>