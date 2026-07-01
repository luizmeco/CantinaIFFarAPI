<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Desativa checagem de chaves estrangeiras para limpar as tabelas com segurança
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
        
        $this->db->table('pedidos_produtos')->truncate();
        $this->db->table('pedidos')->truncate();
        $this->db->table('estoques')->truncate();
        $this->db->table('produtos')->truncate();
        $this->db->table('usuarios')->truncate();
        
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');

        // 1. Popular Usuários
        $usuarios = [
            [
                'email'       => 'admin@admin.com',
                'senha_hash'  => password_hash('admin123', PASSWORD_DEFAULT),
                'tipo'        => 'admin',
                'bloqueado'   => 0,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'email'       => 'cliente@cliente.com',
                'senha_hash'  => password_hash('cliente123', PASSWORD_DEFAULT),
                'tipo'        => 'usuario',
                'bloqueado'   => 0,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'email'       => 'bloqueado@cliente.com',
                'senha_hash'  => password_hash('bloqueado123', PASSWORD_DEFAULT),
                'tipo'        => 'usuario',
                'bloqueado'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('usuarios')->insertBatch($usuarios);

        // 2. Popular Produtos
        $produtos = [
            [
                'nome'       => 'X-Salada',
                'preco'      => 15.00,
                'categoria'  => 'Lanche',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome'       => 'Pastel de Frango',
                'preco'      => 8.50,
                'categoria'  => 'Lanche',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome'       => 'Suco de Laranja',
                'preco'      => 6.00,
                'categoria'  => 'Bebida',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome'       => 'Refrigerante Lata',
                'preco'      => 5.00,
                'categoria'  => 'Bebida',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome'       => 'Batata Frita P',
                'preco'      => 10.00,
                'categoria'  => 'Acompanhamento',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome'       => 'Brigadeiro Gourmet',
                'preco'      => 4.00,
                'categoria'  => 'Sobremesa',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('produtos')->insertBatch($produtos);

        // Recuperar IDs gerados para produtos
        $dbProdutos = $this->db->table('produtos')->get()->getResultArray();
        $produtosIds = array_column($dbProdutos, 'id', 'nome');

        // 3. Popular Estoques
        $estoques = [
            [
                'id_produto' => $produtosIds['X-Salada'],
                'quantidade' => 50,
                'fornecedor' => 'Fornecedor Central',
                'observacao' => 'Carga inicial',
                'tipo'       => 'entrada',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id_produto' => $produtosIds['Pastel de Frango'],
                'quantidade' => 30,
                'fornecedor' => 'Fornecedor Central',
                'observacao' => 'Carga inicial',
                'tipo'       => 'entrada',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id_produto' => $produtosIds['Suco de Laranja'],
                'quantidade' => 100,
                'fornecedor' => 'Distribuidora Bebidas',
                'observacao' => 'Carga inicial',
                'tipo'       => 'entrada',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id_produto' => $produtosIds['Refrigerante Lata'],
                'quantidade' => 80,
                'fornecedor' => 'Distribuidora Bebidas',
                'observacao' => 'Carga inicial',
                'tipo'       => 'entrada',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id_produto' => $produtosIds['Batata Frita P'],
                'quantidade' => 40,
                'fornecedor' => 'Distribuidora Alimentos',
                'observacao' => 'Carga inicial',
                'tipo'       => 'entrada',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id_produto' => $produtosIds['Brigadeiro Gourmet'],
                'quantidade' => 150,
                'fornecedor' => 'Fornecedor Doces',
                'observacao' => 'Carga inicial',
                'tipo'       => 'entrada',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('estoques')->insertBatch($estoques);
    }
}
