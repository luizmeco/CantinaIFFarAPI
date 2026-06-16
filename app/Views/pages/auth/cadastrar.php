<?php $this->extend('templates/auth') ?>

<?php $this->section('title') ?>Cadastrar<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="auth-header">
    <h2>Criar Conta</h2>
    <p>Preencha os dados para se cadastrar</p>
</div>

<?php if(session()->getFlashData('sucesso')): ?>
<div class="alert alert-success">
    <?= session()->getFlashData('sucesso') ?>
</div>
<?php endif; ?>

<?php if(session()->getFlashData('erros')): ?>
<div class="alert alert-danger">
    <?= session()->getFlashData('erros') ?>
</div>
<?php endif; ?>

<form action="<?= site_url('salvar_usuario') ?>" method="POST">
    <?= csrf_field() ?>
    <input type="email" name="email" class="form-control" placeholder="Informe o e-mail" required />
    <input type="password" name="senha" class="form-control" placeholder="Digite sua senha" required />
    <button type="submit" class="btn btn-primary">Cadastrar</button>
</form>

<div class="auth-links">
    <a href="<?= site_url('login') ?>">Já tem conta? Faça login</a>
</div>
<?php $this->endSection() ?>