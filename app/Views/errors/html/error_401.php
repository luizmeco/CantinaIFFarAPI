<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>401 - Acesso Não Autorizado</title>

    <style>
        div.logo {
            height: 200px;
            width: 155px;
            display: inline-block;
            opacity: 0.08;
            position: absolute;
            top: 2rem;
            left: 50%;
            margin-left: -73px;
        }
        body {
            height: 100%;
            background: #fafafa;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #777;
            font-weight: 300;
        }
        h1 {
            font-weight: lighter;
            letter-spacing: normal;
            font-size: 3rem;
            margin-top: 0;
            margin-bottom: 0;
            color: #222;
        }
        .wrap {
            max-width: 1024px;
            margin: 5rem auto;
            padding: 2rem;
            background: #fff;
            text-align: center;
            border: 1px solid #efefef;
            border-radius: 0.5rem;
            position: relative;
        }
        pre {
            white-space: normal;
            margin-top: 1.5rem;
        }
        code {
            background: #fafafa;
            border: 1px solid #efefef;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            display: inline-block;
        }
        .actions {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        .actions a {
            display: inline-block;
            padding: 0.5rem 1rem;
            color: #64b5f6;
            text-decoration: none;
            border-radius: 0.25rem;
            border: 1px solid #64b5f6;
        }
        .actions a:hover {
            background: #64b5f6;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>401 - Acesso Não Autorizado</h1>

        <p>
            Você não tem permissão para acessar esta página.
        </p>

        <div class="actions">
            <a href="<?= base_url('produtos') ?>">Voltar ao Início</a>
            <a href="<?= base_url('logout') ?>">Fazer Login</a>
        </div>
    </div>
</body>
</html>