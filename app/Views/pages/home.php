
<?php $this->extend('templates/admin') ?>

<?php $this->section('title') ?>Home<?php $this->endSection() ?>

<?php $this->section('conteudo') ?>
<h1>Bem-vindo(a)!</h1>
<p>Esta é a página inicial da cantina</p>
<a href="<?= site_url('/produtos') ?>" class="btn btn-primary">Admin produtos</a>
<hr>
<?php $this->endSection() ?>
   