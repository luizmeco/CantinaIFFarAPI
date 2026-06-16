<?php $this->extend('templates/admin') ?>

<?php $this->section('title') ?>Novo Produto<?php $this->endSection() ?>

<?php $this->section('conteudo') ?>

<h1>Novo Produto</h1>

<?php $errors = session()->getFlashdata('errors') ?? [] ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>

<form method='post' action='<?= site_url('admin/produtos/salvar') ?>' enctype='multipart/form-data'>
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="nome" class="form-label">Nome:</label>
        <input type='text' name='nome' id="nome" value='<?= esc(old('nome')) ?>' class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="categoria" class="form-label">Categoria:</label>
        <select name='categoria' id="categoria" class="form-select" required>
            <option value="">Selecione</option>
            <option value="Lanche" <?= old('categoria') == 'Lanche' ? 'selected' : '' ?>>Lanche</option>
            <option value="Bebida" <?= old('categoria') == 'Bebida' ? 'selected' : '' ?>>Bebida</option>
            <option value="Acompanhamento" <?= old('categoria') == 'Acompanhamento' ? 'selected' : '' ?>>Acompanhamento</option>
            <option value="Sobremesa" <?= old('categoria') == 'Sobremesa' ? 'selected' : '' ?>>Sobremesa</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="preco" class="form-label">Preço (R$):</label>
        <input type='number' name='preco' id="preco" step='0.01' value='<?= esc(old('preco')) ?>' class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="foto" class="form-label">Foto do produto:</label>
        <input type='file' name='foto' id="foto" accept='image/*' onchange="previewImagem(event)" class="form-control">
        <img id="preview" src="" class="d-none mt-2" style="max-height:100px; border-radius: 5px;">
    </div>

    <button type='submit' class="btn btn-primary">Cadastrar</button>
    <a href='<?= site_url('produtos') ?>' class="btn btn-secondary ms-2">Cancelar</a>

</form>

<script>
    function previewImagem(event) {
        const file = event.target.files[0];

        if (!file) return;

        const preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('d-none');
    }
</script>

<?php $this->endSection() ?>
        preview.classList.remove('d-none');
    }
</script>

<?= $this->endSection() ?>