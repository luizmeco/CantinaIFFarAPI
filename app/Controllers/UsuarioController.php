<?php
namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\UsuarioModel;

class UsuarioController extends BaseController
{
    protected UsuarioModel $model;

    public function __construct()
    {
        $this->model = new UsuarioModel();
    }

    //função para apresentar o form de cadastro
    public function cadastrar(){
        return view("pages/auth/cadastrar");
    }

    //função para receber os dados de cadastro
    public function salvarUsuario(){
        $data = [
            'email' => $this->request->getPost('email'),
            'senha_hash' => 
                password_hash(
                    $this->request->getPost('senha'),
                    PASSWORD_DEFAULT
                )
        ];

        if(!$this->model->insert($data)){
            return redirect()->back()->withInput()->with('erros', 'Erro ao salvar');
        }
        
        return redirect()->to('/login')->with('sucesso', 'Conta criada. Faça o login');

    }

    //função para apresentar o form de login
    public function login(){
        return view('pages/auth/login');
    }

    //função pra receber os dados de login
    public function autenticar(){
        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');

        //procurar o registro no banco
        $registro = $this->model->where('email', $email)->first();

        if($registro && password_verify($senha, $registro['senha_hash'])){
            session()->set('logado', true); //var de controle
            
            session()->set('usuario', $registro);

            return redirect()->to('/produtos');
        }
        else{
            return redirect()
            ->to('/login')
            ->with('erros', 'Credenciais inválidas')
            ->withInput();
        }
    }

    //função para logout
    public function logout(){
        session()->destroy();
        return redirect()->to('/login')->with('sucesso', 'Vc saiu do sistema');
    }

    //função para apresentar o form de solicitar nova senha
    public function esqueciSenha(){
        return view('pages/auth/esqueci_senha');
    }

    //função para processar a solicitação de redefinição
    public function solicitarReset(){
        $emailRequest = $this->request->getPost('email');
        $usuario = $this->model->where('email', $emailRequest)->first();

        if($usuario){
            $token = bin2hex(random_bytes(50));
            $dataExpiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $this->model->update($usuario['id'], [
                'reset_token' => $token,
                'reset_token_date' => $dataExpiracao
            ]);

            // Configuração de envio de e-mail
            $email = \Config\Services::email();
            $email->setFrom('seuemail@email.com', 'Sistema');
            $email->setTo($usuario['email']);
            $email->setSubject('Redefinição de Senha');
            
            $link = site_url("resetar_senha?token=$token");
            $email->setMessage("
                <h1>Redefinição de Senha</h1>
                <p>Você solicitou a redefinição de sua senha.</p>
                <p>Clique no link abaixo para criar uma nova senha:</p>
                <a href='{$link}'>Redefinir Senha</a>
                <p>Este link é válido por 1 hora.</p>
            ");

            $email->send();
        }

        // Retorna sucesso sempre por segurança (evita que descubram quais e-mails existem na base)
        return redirect()->to('/login')->with('sucesso', 'Se o e-mail existir em nossa base, enviaremos um link de recuperação.');
    }

    //função para apresentar o form de cadastro da nova senha
    public function resetarSenha(){
        $token = $this->request->getGet('token');
        
        // Verifica se existe o token e se a data de expiração é maior ou igual ao momento atual
        $usuario = $this->model->where('reset_token', $token)
                               ->where('reset_token_date >=', date('Y-m-d H:i:s'))
                               ->first();

        if(!$usuario || empty($token)){
            return redirect()->to('/login')->with('erros', 'Token inválido ou expirado.');
        }

        return view('pages/auth/resetar_senha', ['token' => $token]);
    }

    //função para efetivamente salvar a nova senha
    public function salvarNovaSenha(){
        $token = $this->request->getPost('token');
        $senha = $this->request->getPost('senha');
        $confirmacao = $this->request->getPost('confirmar_senha');

        if($senha !== $confirmacao){
            return redirect()->back()->with('erros', 'As senhas não conferem.');
        }

        // Verifica novamente a integridade do token antes de permitir salvar
        $usuario = $this->model->where('reset_token', $token)
                               ->where('reset_token_date >=', date('Y-m-d H:i:s'))
                               ->first();

        if(!$usuario || empty($token)){
            return redirect()->to('/login')->with('erros', 'Token inválido ou expirado.');
        }

        // Salva o novo hash da senha e limpa o token gerado
        $this->model->update($usuario['id'], [
            'senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_token_date' => null
        ]);

        return redirect()->to('/login')->with('sucesso', 'Sua senha foi alterada com sucesso! Faça login.');
    }

}