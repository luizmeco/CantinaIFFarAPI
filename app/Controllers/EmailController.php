<?php

namespace App\Controllers;

use App\Controllers\BaseController;


class EmailController extends BaseController
{

    public function enviar()
    {
        $email = \Config\Services::email();

        $email->setFrom('seuemail@email.com', 'Fulano ');
        $email->setTo('fulano@gmail.com');

        $email->setSubject('Teste de envio pelo CodeIgniter');

        $email->setMessage('
            <h1>Teste</h1>
            <p>Este é um teste de envio usando CodeIgniter .</p>
        ');

        if ($email->send()) {
            echo 'E-mail enviado com sucesso!';
        } else {
            echo '<pre>';
            print_r($email->printDebugger(['headers', 'subject', 'body']));
            echo '</pre>';
        }
    }
}
