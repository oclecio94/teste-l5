<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class Cliente extends ResourceController
{

    private $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new \App\Models\ClienteModel();
    }

    public function index()
    {
        $clientes = $this->clienteModel->findAll();
    
        $response = [
              'cabecalho' => [
                    'status' => 200,
                    'mensagem' => 'Dados retornados com sucesso'
                             ],
              'retorno' => $clientes
                   ];
    
         return $this->response->setJSON($response);
    }

    public function show($id = null)
    {
        $data = $this->clienteModel->find($id);
        if (!$data) {
            $response = [
                'cabecalho' => [
                      'status' => 404,
                      'mensagem' => 'Cliente não encontrado'
                               ],
                'retorno' => $data
                      ];

            return $this->response->setJSON($response);
        }

        $response = [
            'cabecalho' => [
                  'status' => 200,
                  'mensagem' => 'Dados retornados com sucesso'
                           ],
            'retorno' => $data
                 ];

        return $this->response->setJSON($response);
    }

    public function create()
    {
        $request = $this->request->getJSON(true);

        $existingClient = $this->clienteModel->where('cpf_cnpj', $request['cpf_cnpj'])->first();

        if ($existingClient) {
           $response = [
            'cabecalho' => [
                'status' => 400,
                'mensagem' => 'CPF ou CNPJ já cadastrado'
            ],
            'retorno' => null
           ];
        return $this->response->setJSON($response);
        }

        $data = $this->clienteModel->insert($request);

        $clientId = $this->clienteModel->getInsertID();
    
        if (!$clientId) {
            $response = [
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Erro ao criar cliente'
                ],
                'retorno' => null
            ];
            return $this->response->setJSON($response);
        }

        $client = $this->clienteModel->find($clientId);
    
        $response = [
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Cliente criado com sucesso'
            ],
            'retorno' => $client 
        ];
    
        return $this->response->setJSON($response);
    }

    public function update($id = null)
    {
        $request = $this->request->getJSON(true);

        $client = $this->clienteModel->find($id);

        if (!$client) {
            $response = [
                'cabecalho' => [
                      'status' => 404,
                      'mensagem' => 'Cliente nao encontrado'
                               ],
                'retorno' => null
                      ];

            return $this->response->setJSON($response);
        }

        $existingClient = $this->clienteModel->where('cpf_cnpj', $request['cpf_cnpj'])->where('id !=', $id)->first();

        if ($existingClient) {
              $response = [
                 'cabecalho' => [
                 'status' => 409,
                 'mensagem' => 'Já existe um cliente com o mesmo CPF ou CNPJ.'
                      ],
                  'retorno' => null
                    ];
               return $this->response->setJSON($response);
           }

        $this->clienteModel->update($id, $request);

        $updatedClient = $this->clienteModel->find($id);

        if (!$updatedClient) {
          $response = [
                'cabecalho' => [
                'status' => 404,
                'mensagem' => 'Erro ao atualizar cliente'
            ],
            'retorno' => null
            ];
           return $this->response->setJSON($response);
         }

        $response = [
            'cabecalho' => [
                  'status' => 200,
                  'mensagem' => 'Cliente atualizado com sucesso'
                           ],
            'retorno' => $updatedClient
                 ];

        return $this->response->setJSON($response);
    }

    public function delete($id = null)
    {
        $client = $this->clienteModel->find($id);
        if (!$client) {
            $response = [
                'cabecalho' => [
                      'status' => 404,
                      'mensagem' => 'Cliente nao encontrado'
                               ],
                'retorno' => null
                      ];

            return $this->response->setJSON($response);
        }

       $this->clienteModel->delete($id);

        $response = [
            'cabecalho' => [
                  'status' => 200,
                  'mensagem' => 'Cliente deletado com sucesso'
                           ],
            'retorno' => $client
                 ];

        return $this->response->setJSON($response);
    }
}
