<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cantina - <?= $this->renderSection('title') ?: 'Admin' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-desktop {
            background: white;
            min-height: 100vh;
            border-right: 1px solid #e0e0e0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
        }

        .menu-link {
            display: block;
            padding: 12px 15px;
            text-decoration: none;
            color: #333;
            border-radius: 10px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .menu-link:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateX(5px);
        }

        .menu-link.ativo {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        }

        .main-content {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin: 20px 0;
            padding: 30px;
            min-height: calc(100vh - 120px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 12px 15px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .offcanvas {
            background: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>

    <?= $this->renderSection('styles') ?>
</head>
<body>

    <!-- navbar -->
    <nav class="navbar navbar-dark px-3">
        <div class="d-flex align-items-center">
            <!-- botão mobile -->
            <button class="btn btn-outline-light d-md-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuMobile" aria-controls="menuMobile">
                ☰
            </button>

            <a class="navbar-brand mb-0" href="<?= site_url('/') ?>">
                Cantina
            </a>
        </div>

        <span class="text-white">
            <?= session()->get('logado') ? session()->get('usuario')['email'] : '' ?>
        </span>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <aside class="col-md-3 col-lg-2 sidebar-desktop p-3 d-none d-md-block">
                <h6 class="text-muted mb-3">Menu</h6>

                <a href="<?= site_url('produtos') ?>" class="menu-link <?= url_is('produtos*') || url_is('admin/produtos*') ? 'ativo' : '' ?>">Produtos</a>

                <?php if(session()->get('usuario')['tipo'] === 'admin'): ?>
                    <a href="<?= site_url('admin/usuarios') ?>" class="menu-link <?= url_is('admin/usuarios*') ? 'ativo' : '' ?>">Usuários</a>
                    <a href="<?= site_url('admin/estoque') ?>" class="menu-link <?= url_is('admin/estoque*') ? 'ativo' : '' ?>">Estoque</a>
                    <a href="<?= site_url('admin/vendas') ?>" class="menu-link <?= url_is('admin/vendas*') ? 'ativo' : '' ?>">Vendas</a>
                <?php endif; ?>

                <a href="<?= site_url('meus-dados') ?>" class="menu-link <?= url_is('meus-dados*') ? 'ativo' : '' ?>">Meus Dados</a>

                <?php if(session()->get('logado')): ?>
                    <a href="<?= site_url('logout') ?>" class="menu-link">Sair</a>
                <?php endif; ?>
            </aside>

            <!-- conteúdo das views aqui -->
            <main class="col-12 col-md-9 col-lg-10 px-3 px-md-4 py-4">
                <div class="main-content">
                    <?= $this->renderSection('conteudo') ?>
                </div>
            </main>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="menuMobile" aria-labelledby="menuMobileLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="menuMobileLabel">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
        </div>

        <div class="offcanvas-body">
            <a href="<?= site_url('produtos') ?>" class="menu-link <?= url_is('produtos*') || url_is('admin/produtos*') ? 'ativo' : '' ?>">Produtos</a>
            <?php if(session()->get('usuario')['tipo'] === 'admin'): ?>
                <a href="<?= site_url('admin/usuarios') ?>" class="menu-link <?= url_is('admin/usuarios*') ? 'ativo' : '' ?>">Usuários</a>
                <a href="<?= site_url('admin/estoque') ?>" class="menu-link <?= url_is('admin/estoque*') ? 'ativo' : '' ?>">Estoque</a>
                <a href="<?= site_url('admin/vendas') ?>" class="menu-link <?= url_is('admin/vendas*') ? 'ativo' : '' ?>">Vendas</a>
            <?php endif; ?>
            <a href="<?= site_url('meus-dados') ?>" class="menu-link <?= url_is('meus-dados*') ? 'ativo' : '' ?>">Meus Dados</a>
            <?php if(session()->get('logado')): ?>
                <a href="<?= site_url('logout') ?>" class="menu-link">Sair</a>
            <?php endif; ?>
        </div>
    </div>

    <?= $this->renderSection('scripts') ?>
</body>
</html>