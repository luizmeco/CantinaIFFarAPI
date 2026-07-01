# 🍽️ Cantina IFFar - API

Este é o repositório da API do sistema **Cantina IFFar**, desenvolvida utilizando o framework **CodeIgniter 4**. Ela serve como o backend centralizador das regras de negócio, gerenciamento de banco de dados e endpoints para os clientes (totem) e painel da cozinha.

---

## 🚀 Como Executar o Projeto

Siga as instruções abaixo para configurar e rodar a API localmente em sua máquina.

### 📋 Pré-requisitos
Antes de começar, certifique-se de possuir instalado em seu ambiente:
* **PHP** (versão 8.1 ou superior recomendada)
* **Composer**
* Banco de dados **MySQL / MariaDB** (ou servidor XAMPP/WampServer ativo)

---

## 🛠️ Passo a Passo para Configuração

> [!NOTE]
> Todos os comandos abaixo devem ser executados no **Prompt de Comando (cmd)** ou terminal de sua preferência, dentro da pasta raiz deste projeto (`CantinaIFFarAPI`).

### 1. Clonar e Acessar o Diretório
Se você ainda não estiver na pasta do projeto:
```cmd
cd CantinaIFFarAPI
```

### 2. Configurar o Arquivo de Ambiente (.env)
Copie o arquivo de exemplo `.env.example` criando o seu arquivo `.env` definitivo:
```cmd
copy .env.example .env
```

> [!IMPORTANT]
> Abra o arquivo `.env` recém-criado e configure as credenciais do seu banco de dados local nas seguintes chaves:
> * `database.default.hostname = localhost`
> * `database.default.database = nome_do_seu_banco`
> * `database.default.username = seu_usuario`
> * `database.default.password = sua_senha`

### 3. Instalar as Dependências do Composer
Baixe todas as dependências e bibliotecas necessárias para o projeto rodar:
```cmd
composer install
```

### 4. Executar as Migrations e Seeds
Para criar a estrutura das tabelas no banco de dados e populá-lo com os dados iniciais obrigatórios (como usuários e produtos base), execute os seguintes comandos:

```cmd
php spark migrate
php spark db:seed DatabaseSeeder
```

### 5. Iniciar o Servidor de Desenvolvimento
Para rodar a API localmente com o servidor embutido do CodeIgniter:
```cmd
php spark serve
```

A API estará acessível por padrão em: `http://localhost:8080`

---

## 📦 Estrutura do Banco de Dados

As migrations configuradas criam as seguintes tabelas estruturais:
* **TabelaProdutos**: Gerenciamento de itens de consumo da cantina.
* **TabelaUsuarios**: Credenciais e permissões dos usuários do sistema.
* **TabelaEstoque**: Controle e movimentações de estoque.
* **TabelaPedidos**: Registro de compras efetuadas.
* **TabelaPedidosProduto**: Itens vinculados a cada pedido.
