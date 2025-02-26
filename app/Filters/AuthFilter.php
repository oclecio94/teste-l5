<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');

        if (!$header) {
            return service('response')->setJSON([
                'cabecalho' => [
                    'status' => 401,
                    'mensagem' => 'Token não fornecido'
                ],
            ])->setStatusCode(401);
        }

        $token = str_replace('Bearer ', '', $header);

        $secretKey = getenv('JWT_SECRET');

        try {
            JWT::decode($token, new Key($secretKey, 'HS256'));
        } catch (Exception $e) {
            return service('response')->setJSON([
                'status' => 401,
                'message' => 'Token inválido ou expirado'
            ])->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
