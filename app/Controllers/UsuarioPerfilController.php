<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class UsuarioPerfilController extends BaseController
{
    protected UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    // Exibir tela de alteração dos próprios dados
    public function index()
    {
        $this->verificarLogin();

        return view('pages/perfil/editar');
    }

    // Salvar novos dados do próprio usuário
    public function salvar()
    {
        $this->verificarLogin();

        $usuarioLogado = session()->get('usuario');
        $id = $usuarioLogado['id'];

        $regras = [
            'email' => "required|valid_email|is_unique[usuarios.email,id,{$id}]"
        ];

        $mensagens = [
            'email' => [
                'required'    => 'O e-mail é obrigatório.',
                'valid_email' => 'Informe um e-mail válido.',
                'is_unique'   => 'Este e-mail já está sendo utilizado por outra conta.'
            ]
        ];

        $senha = $this->request->getPost('senha');
        $confirmar = $this->request->getPost('confirmar_senha');

        if (!empty($senha)) {
            $regras['senha'] = 'min_length[6]';
            $regras['confirmar_senha'] = 'matches[senha]';

            $mensagens['senha'] = [
                'min_length' => 'A nova senha deve conter no mínimo 6 caracteres.'
            ];
            $mensagens['confirmar_senha'] = [
                'matches' => 'A confirmação de senha não confere.'
            ];
        }

        if (!$this->validate($regras, $mensagens)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $dados = [
            'email' => $this->request->getPost('email')
        ];

        if (!empty($senha)) {
            $dados['senha_hash'] = password_hash($senha, PASSWORD_DEFAULT);
        }

        if (!$this->usuarioModel->update($id, $dados)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['database' => 'Erro ao salvar alterações no banco de dados.']);
        }

        // Atualiza os dados na sessão com o usuário atualizado
        $usuarioAtualizado = $this->usuarioModel->find($id);
        session()->set('usuario', $usuarioAtualizado);

        return redirect()->to(site_url('meus-dados'))
            ->with('sucesso', 'Seus dados foram atualizados com sucesso!');
    }
}
