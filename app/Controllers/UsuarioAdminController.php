<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class UsuarioAdminController extends BaseController
{
    protected UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    // Listar usuários
    public function index()
    {
        $this->verificarLogin();

        $usuarios = $this->usuarioModel->paginate(10);

        return view('pages/admin/usuarios/index', [
            'usuarios' => $usuarios,
            'pager'    => $this->usuarioModel->pager
        ]);
    }

    // Tela de novo usuário
    public function novo()
    {
        $this->verificarLogin();

        return view('pages/admin/usuarios/cadastro');
    }

    // Salvar novo usuário
    public function salvar()
    {
        $this->verificarLogin();

        $regras = [
            'email' => 'required|valid_email|is_unique[usuarios.email]',
            'senha' => 'required|min_length[6]',
            'tipo'  => 'required|in_list[admin,usuario]'
        ];

        $mensagens = [
            'email' => [
                'required'    => 'O e-mail é obrigatório.',
                'valid_email' => 'Informe um e-mail válido.',
                'is_unique'   => 'Este e-mail já está cadastrado no sistema.'
            ],
            'senha' => [
                'required'   => 'A senha é obrigatória.',
                'min_length' => 'A senha deve conter no mínimo 6 caracteres.'
            ],
            'tipo' => [
                'required' => 'O tipo de usuário é obrigatório.',
                'in_list'  => 'O tipo selecionado é inválido.'
            ]
        ];

        if (!$this->validate($regras, $mensagens)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'email'      => $this->request->getPost('email'),
            'senha_hash' => password_hash($this->request->getPost('senha'), PASSWORD_DEFAULT),
            'tipo'       => $this->request->getPost('tipo'),
            'bloqueado'  => 0
        ];

        if (!$this->usuarioModel->insert($dados)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['database' => 'Erro ao salvar no banco de dados.']);
        }

        return redirect()->to(site_url('admin/usuarios'))
            ->with('sucesso', 'Usuário cadastrado com sucesso!');
    }

    // Tela de edição de usuário
    public function editar(int $id)
    {
        $this->verificarLogin();

        $usuario = $this->usuarioModel->find($id);

        if (!$usuario) {
            return redirect()->to(site_url('admin/usuarios'))
                ->with('erro', 'Usuário não encontrado.');
        }

        return view('pages/admin/usuarios/editar', [
            'usuario' => $usuario
        ]);
    }

    // Atualizar usuário
    public function atualizar(int $id)
    {
        $this->verificarLogin();

        $usuario = $this->usuarioModel->find($id);

        if (!$usuario) {
            return redirect()->to(site_url('admin/usuarios'))
                ->with('erro', 'Usuário não encontrado.');
        }

        $regras = [
            'email' => "required|valid_email|is_unique[usuarios.email,id,{$id}]",
            'tipo'  => 'required|in_list[admin,usuario]'
        ];

        $mensagens = [
            'email' => [
                'required'    => 'O e-mail é obrigatório.',
                'valid_email' => 'Informe um e-mail válido.',
                'is_unique'   => 'Este e-mail já está cadastrado no sistema.'
            ],
            'tipo' => [
                'required' => 'O tipo de usuário é obrigatório.',
                'in_list'  => 'O tipo selecionado é inválido.'
            ]
        ];

        // Se o usuário estiver editando a si mesmo, impede a alteração do tipo para evitar a perda do acesso administrativo
        if ($id == session()->get('usuario')['id']) {
            $tipoPost = 'admin'; // Força admin
        } else {
            $tipoPost = $this->request->getPost('tipo');
        }

        // Validação da senha se fornecida
        $senha = $this->request->getPost('senha');
        if (!empty($senha)) {
            $regras['senha'] = 'min_length[6]';
            $mensagens['senha'] = [
                'min_length' => 'A senha deve conter no mínimo 6 caracteres.'
            ];
        }

        if (!$this->validate($regras, $mensagens)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'email' => $this->request->getPost('email'),
            'tipo'  => $tipoPost
        ];

        if (!empty($senha)) {
            $dados['senha_hash'] = password_hash($senha, PASSWORD_DEFAULT);
        }

        if (!$this->usuarioModel->update($id, $dados)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['database' => 'Erro ao atualizar dados no banco de dados.']);
        }

        return redirect()->to(site_url('admin/usuarios'))
            ->with('sucesso', 'Usuário atualizado com sucesso!');
    }

    // Bloquear usuário
    public function bloquear(int $id)
    {
        $this->verificarLogin();

        // Impede de se autobloquear
        if ($id == session()->get('usuario')['id']) {
            return redirect()->to(site_url('admin/usuarios'))
                ->with('erro', 'Você não pode bloquear a sua própria conta.');
        }

        $usuario = $this->usuarioModel->find($id);

        if (!$usuario) {
            return redirect()->to(site_url('admin/usuarios'))
                ->with('erro', 'Usuário não encontrado.');
        }

        $this->usuarioModel->update($id, ['bloqueado' => 1]);

        return redirect()->to(site_url('admin/usuarios'))
            ->with('sucesso', 'Usuário bloqueado com sucesso!');
    }

    // Desbloquear usuário
    public function desbloquear(int $id)
    {
        $this->verificarLogin();

        $usuario = $this->usuarioModel->find($id);

        if (!$usuario) {
            return redirect()->to(site_url('admin/usuarios'))
                ->with('erro', 'Usuário não encontrado.');
        }

        $this->usuarioModel->update($id, ['bloqueado' => 0]);

        return redirect()->to(site_url('admin/usuarios'))
            ->with('sucesso', 'Usuário desbloqueado com sucesso!');
    }
}
