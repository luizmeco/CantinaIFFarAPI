<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\ProdutoModel;


class ProdutoController extends BaseController
{

    protected ProdutoModel $model;

    public function __construct()
    {
        $this->model = new ProdutoModel();
    }

    //Listar todos os produtos
    public function index(): string
    {
        //verificar a sessão
        //redirecionar o usuário
        $this->verificarLogin();


        //ANTES
        //$produtos = $this->model->findAll();

        //COM BUSCA
        $busca = $this->request->getGet('busca');
        $preco = $this->request->getGet('preco');
        $categoria = $this->request->getGet('categoria');

        if ($busca) {
            $this->model->like('nome', $busca);
        }

        if (!empty($preco)) {
            if ($preco == 'baixo') {
                $this->model->where('preco <', 5);
            } elseif ($preco == 'medio') {
                $this->model->where('preco >=', 5)->where('preco <=', 10);
            } elseif ($preco == 'alto') {
                $this->model->where('preco >', 10);
            }
        }

        if (!empty($categoria)) {
            $this->model->where('categoria', $categoria);
        }

        //AGORA
        $produtos =  $this->model->paginate(10);
        return view('pages/produtos/index', [
                'produtos' => $produtos,
                'pager'   =>  $this->model->pager,
                'busca'   => $busca,
                'preco' => $preco,
                'categoria' => $categoria

            ]
        );
    }


    public function novo(): string
    {
        return view('pages/produtos/cadastro', [
            'titulo' => 'Novo Produto',
            'produto' => null,
        ]);
    }

    //salva um novo produto
    public function salvar()
    {


        $dados = [
            'nome'      => $this->request->getPost('nome'),
            'preco'     => $this->request->getPost('preco'),
            'categoria' => $this->request->getPost('categoria')
        ];

        $regrasFoto = [
            'foto'  => 'is_image[foto]'
                . '|mime_in[foto,image/jpeg,image/png,image/gif]'
                . '|ext_in[foto,jpg,jpeg,png,webp]'
                . '|max_size[foto,2048]'
        ];

        $erros = [];

        if (!$this->model->validate($dados)) {
            $erros = array_merge($erros, $this->model->errors());
        }

        if (! $this->validate($regrasFoto)) {
            $erros = array_merge($erros, $this->validator->getErrors());
        }

        if (! empty($erros)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $erros);
        }


        $foto = $this->request->getFile('foto');

        if ($foto && $foto->isValid()) {

            $nomeFotoRandomico = $foto->getRandomName();

            $foto->move(FCPATH . 'uploads/produtos', $nomeFotoRandomico);

            $dados = [
                'nome'      => $this->request->getPost('nome'),
                'preco'     => $this->request->getPost('preco'),
                'categoria' => $this->request->getPost('categoria'),
                'foto'      => $nomeFotoRandomico
            ];
        }

        if (! $this->model->insert($dados)) {
            if (isset($nomeFotoRandomico)) {
                unlink(FCPATH . 'uploads/produtos/' . $nomeFotoRandomico);
            }
            return redirect()->back()->withInput()
                ->with('errors', $this->model->errors());
        }

        return redirect()->to(site_url('produtos'));
    }




    //abre a página de edição
    public function editar(int $id): string
    {

        $produto = $this->model->find($id);

        return view('pages/produtos/editar', [
            'titulo' => 'Editar Produto',
            'produto' => $produto
        ]);
    }


    //atualiza um produto
    public function atualizar(int $id)
    {
        $produto = $this->model->find($id);

        $dados = [
            'nome'      => $this->request->getPost('nome'),
            'preco'     => $this->request->getPost('preco'),
            'categoria' => $this->request->getPost('categoria'),
        ];

        $regrasFoto = [
            'foto'  => 'is_image[foto]'
                . '|mime_in[foto,image/jpeg,image/png,image/gif]'
                . '|ext_in[foto,jpg,jpeg,png,webp]'
                . '|max_size[foto,2048]'
        ];

        $erros = [];

        if (!$this->model->validate($dados)) {
            $erros = array_merge($erros, $this->model->errors());
        }

        // Validar foto apenas se uma nova foi enviada
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->getError() != 4) { // 4 = no file selected
            if (! $this->validate($regrasFoto)) {
                $erros = array_merge($erros, $this->validator->getErrors());
            }
        }

        if (! empty($erros)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $erros);
        }

        // Processar a nova imagem se enviada
        if ($foto && $foto->getError() == 0) {
            $nomeFotoRandomico = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/produtos', $nomeFotoRandomico);

            // Deletar foto antiga se existir
            if (!empty($produto['foto'])) {
                $caminhoAntigo = FCPATH . 'uploads/produtos/' . $produto['foto'];
                if (file_exists($caminhoAntigo)) {
                    unlink($caminhoAntigo);
                }
            }

            $dados['foto'] = $nomeFotoRandomico;
        }

        if (! $this->model->update($id, $dados)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->model->errors());
        }

        return redirect()->to(site_url('produtos'));
    }


    public function excluir(int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $produto = $this->model->find($id);

        if (! empty($produto['foto'])) {
            $caminho = FCPATH . 'uploads/produtos/' . $produto['foto'];
            if (file_exists($caminho)) {
                unlink($caminho);
            }
        }

        $this->model->delete($id);
        return redirect()->to(site_url('produtos'));
}
}