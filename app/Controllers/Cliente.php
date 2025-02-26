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
        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 10;
        $search = $this->request->getGet('search');

        $query = $this->clienteModel;

        if ($search) {
            $query = $query->groupStart()
                ->like('cpf_cnpj', $search)
                ->orLike('nome_razao_social', $search)
                ->groupEnd();
        }

        $clientes = $query->paginate($perPage, 'default', $page);
        $pager = $query->pager;

        $response = [
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Dados retornados com sucesso'
            ],
            'retorno' => [
                'dados' => $clientes,
                'paginacao' => [
                    'current_page' => $pager->getCurrentPage(),
                    'per_page' => $pager->getPerPage(),
                    'total_items' => $pager->getTotal(),
                    'last_page' => $pager->getLastPage(),
                    'next' => $pager->getNextPageURI(),
                    'previous' => $pager->getPreviousPageURI()
                ]
            ]
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

        if (!isset($request['parametros']) || empty($request['parametros'])) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'Parâmetros inválidos ou ausentes'
                ],
            ])->setStatusCode(400);
        }

        $parametros = $request['parametros'];

        $existingClient = $this->clienteModel->where('cpf_cnpj', $parametros['cpf_cnpj'])->first();

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

        $data = $this->clienteModel->insert($parametros);

        $clientId = $this->clienteModel->getInsertID();

        if (!$clientId) {
            $response = [
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Erro ao cadastrar cliente'
                ],
                'retorno' => null
            ];
            return $this->response->setJSON($response);
        }

        $client = $this->clienteModel->find($clientId);

        $response = [
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Cliente cadastrado com sucesso'
            ],
            'retorno' => $client
        ];

        return $this->response->setJSON($response);
    }

    public function update($id = null)
    {
        $request = $this->request->getJSON(true);

        if (!isset($request['parametros']) || empty($request['parametros'])) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'Parâmetros inválidos ou ausentes'
                ],
            ])->setStatusCode(400);
        } 

        $parametros = $request['parametros'];

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

        $existingClient = $this->clienteModel->where('cpf_cnpj', $parametros['cpf_cnpj'])->where('id !=', $id)->first();

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

        $this->clienteModel->update($id, $parametros);

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
