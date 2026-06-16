<?php $this->extend('templates/auth') ?>

<?php $this->section('title') ?>Recuperar Senha<?php $this->endSection() ?>

<?php $this->section('content') ?>
<div class="auth-header">
    <h2>Recuperar Senha</h2>
    <p>Informe seu e-mail para receber as instruções</p>
</div>

<?php if(session()->getFlashData('sucesso')): ?>
<div class="alert alert-success">
    <?= session()->getFlashData('sucesso') ?>
</div>
<?php endif; ?>

<form action="<?= site_url('solicitar_reset') ?>" method="POST">
    <?= csrf_field() ?>
    <input type="email" name="email" class="form-control" placeholder="Informe seu e-mail cadastrado" required />
    <button type="submit" class="btn btn-primary">Solicitar redefinição</button>
</form>

<div class="auth-links">
    <a href="<?= site_url('login') ?>">Voltar ao login</a>
</div>
<?php $this->endSection() ?>