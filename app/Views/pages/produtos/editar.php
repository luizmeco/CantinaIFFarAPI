<?php $this->extend('templates/admin') ?>

<?php $this->section('title') ?>Editar Produto<?php $this->endSection() ?>

<?php $this->section('conteudo') ?>

<h1>Editar Produto</h1>

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

<form method='post' action='<?= site_url('admin/produtos/atualizar/' . $produto['id']) ?>' enctype='multipart/form-data'>
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="nome" class="form-label">Nome:</label>
        <input type='text' name='nome' id="nome" value='<?= esc(old('nome', $produto['nome'])) ?>' class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="categoria" class="form-label">Categoria:</label>
        <select name='categoria' id="categoria" class="form-select" required>
            <option value="Lanche" <?= old('categoria', $produto['categoria']) == 'Lanche' ? 'selected' : '' ?>>Lanche</option>
            <option value="Bebida" <?= old('categoria', $produto['categoria']) == 'Bebida' ? 'selected' : '' ?>>Bebida</option>
            <option value="Acompanhamento" <?= old('categoria') == 'Acompanhamento' ? 'selected' : '' ?>>Acompanhamento</option>
            <option value="Sobremesa" <?= old('categoria') == 'Sobremesa' ? 'selected' : '' ?>>Sobremesa</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="preco" class="form-label">Preço (R$):</label>
        <input type='number' name='preco' id="preco" step='0.01' value='<?= esc(old('preco', $produto['preco'])) ?>' class="form-control" required>
    </div>

    <!-- exibe a foto atual se existir -->
    <?php if (!empty($produto['foto'])): ?>
        <div class="mb-3">
            <label class="form-label">Foto atual:</label><br>
            <img src='<?= base_url('uploads/produtos/' . esc($produto['foto'])) ?>' style='width:100px; height:100px; object-fit:cover; border-radius: 5px;'><br>
            <small class="text-muted">Deixe em branco para manter a foto atual</small>
        </div>
    <?php endif ?>

    <div class="mb-3">
        <label for="foto" class="form-label">Nova foto (opcional):</label>
        <input type='file' name='foto' id="foto" accept='image/*' class="form-control">
    </div>

    <button type='submit' class="btn btn-primary">Salvar Alterações</button>
    <a href='<?= site_url('produtos') ?>' class="btn btn-secondary ms-2">Cancelar</a>
</form>

<?php $this->endSection() ?>