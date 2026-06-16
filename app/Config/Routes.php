<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('produtos', 'ProdutoController::index');

//rotas para cadastro e login
$routes->get('cadastrar', 'UsuarioController::cadastrar');

$routes->post('salvar_usuario', 'UsuarioController::salvarUsuario');

$routes->get('login', 'UsuarioController::login');

$routes->post('login', 'UsuarioController::autenticar');

$routes->get('logout', 'UsuarioController::logout');

//rotas para recuperação de senha
$routes->get('esqueci_senha', 'UsuarioController::esqueciSenha');

$routes->post('solicitar_reset', 'UsuarioController::solicitarReset');

$routes->get('resetar_senha', 'UsuarioController::resetarSenha');

$routes->post('salvar_nova_senha', 'UsuarioController::salvarNovaSenha');

$routes->get('email/teste', 'EmailController::enviar');

$routes->group('admin', ['filter' => 'admin:admin'], function($routes) {

    $routes->get('produtos/editar/(:num)',  'ProdutoController::editar/$1');

    $routes->post('produtos/atualizar/(:num)', 'ProdutoController::atualizar/$1');

    $routes->get('produtos/excluir/(:num)', 'ProdutoController::excluir/$1');

    $routes->get('produtos/novo', 'ProdutoController::novo');

    $routes->post('produtos/salvar', 'ProdutoController::salvar');

});

// estoque
$routes->get('estoque', 'EstoqueController::index');

$routes->get('estoque/adicionar/(:num)', 'EstoqueController::adicionar/$1');

$routes->get('estoque/remover/(:num)', 'EstoqueController::remover/$1');

$routes->post('estoque/salvar', 'EstoqueController::salvar');

$routes->get('estoque/historico/(:num)', 'EstoqueController::historico/$1');

//rotas de API
$routes->group('api', function($routes) {

    $routes->get('status', 'Api\ApiController::api_status');

    $routes->get('produtos', 'Api\ApiController::get_produtos');

    $routes->post('checkout', 'Api\ApiController::checkout');

    $routes->post('adicionarAoCarrinho', 'Api\ApiController::adicionarAoCarrinho');

    $routes->post('removerItemPedido', 'Api\ApiController::removerItemPedido');

    $routes->post('atualizarItemPedido', 'Api\ApiController::atualizarItemPedido');

    $routes->post('finalizarPedido', 'Api\ApiController::limparPedido');

});