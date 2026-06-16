<?php $this->extend('templates/auth') ?>

<?php $this->section('title') ?>Login<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="auth-header">
    <h2>Bem-vindo de volta</h2>
    <p>Faça login para acessar sua conta</p>
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

<form action="<?= site_url('login') ?>" method="POST">
    <?= csrf_field() ?>
    <input type="email" name="email" class="form-control" placeholder="Informe o e-mail" value="<?= old('email') ?>" required />
    <input type="password" name="senha" class="form-control" placeholder="Digite sua senha" required />
    <button type="submit" class="btn btn-primary">Entrar</button>
</form>

<div class="auth-links">
    <a href="<?= site_url('cadastrar') ?>">Não tem conta? Cadastre-se</a>
</div>
<div class="auth-links">
    <a href="<?= site_url('esqueci_senha') ?>">Recuperar senha</a>
</div>
<?php $this->endSection() ?>