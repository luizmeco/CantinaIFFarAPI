<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminMiddleware implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $usuario = session()->get('usuario');

        if(!$usuario || !in_array($usuario['tipo'], $arguments)){
            $response = service('response');
            $response->setStatusCode(403);
            $response->setBody(view('errors/html/error_401'));
            return $response;
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {

    }
}
?>