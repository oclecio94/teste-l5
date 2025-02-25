<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class Produto extends ResourceController
{
    private $produtoModel;

    public function __construct()
    {
        $this->produtoModel = new \App\Models\ProdutoModel();
    }

    public function index()
    {
        $page = $this->request->getGet('page') ?? 1; 
        $perPage = $this->request->getGet('per_page') ?? 10; 
        $search = $this->request->getGet('search');

        $query = $this->produtoModel;

        if ($search) {
            $query = $query->like('nome', $search);
        }
     
        $produtos = $query->paginate($perPage, 'default', $page);
        $pager = $query->pager;
    
        $response = [
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Dados retornados com sucesso'
            ],
            'retorno' => [ 
                'dados' => $produtos,
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
        $data = $this->produtoModel->find($id);
        if (!$data) {
            $response = [
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Produto não encontrado'
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
        $parametros = $request['parametros'];

        $existingProduct = $this->produtoModel->where('nome', $parametros['nome'])->first();

        if ($existingProduct) {
            $response = [
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'Produto já cadastrado'
                ],
                'retorno' => null
            ];
            return $this->response->setJSON($response);
        }

        $data = $this->produtoModel->insert($parametros);

        $productId = $this->produtoModel->getInsertID();

        if (!$productId) {
            $response = [
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Erro ao criar produto'
                ],
                'retorno' => null
            ];
            return $this->response->setJSON($response);
        }

        $product = $this->produtoModel->find($productId);

        $response = [
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Produto criado com sucesso'
            ],
            'retorno' => $product
        ];

        return $this->response->setJSON($response);
    }

    public function update($id = null)
    {
        $request = $this->request->getJSON(true);
        $parametros = $request['parametros'];

        $product = $this->produtoModel->find($id);

        if (!$product) {
            $response = [
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Produto não encontrado'
                ],
                'retorno' => null
            ];

            return $this->response->setJSON($response);
        }

        $existingProduct = $this->produtoModel->where('nome', $parametros['nome'])->where('id !=', $id)->first();

        if ($existingProduct) {
            $response = [
                'cabecalho' => [
                    'status' => 409,
                    'mensagem' => 'Já existe um produto com o mesmo nome'
                ],
                'retorno' => null
            ];
            return $this->response->setJSON($response);
        }

        $this->produtoModel->update($id, $parametros);

        $updatedProduct = $this->produtoModel->find($id);

        if (!$updatedProduct) {
            $response = [
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Erro ao atualizar produto'
                ],
                'retorno' => null
            ];
            return $this->response->setJSON($response);
        }

        $response = [
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Produto atualizado com sucesso'
            ],
            'retorno' => $updatedProduct
        ];

        return $this->response->setJSON($response);
    }

    public function delete($id = null)
    {
        $product = $this->produtoModel->find($id);
        if (!$product) {
            $response = [
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Produto não encontrado'
                ],
                'retorno' => null
            ];

            return $this->response->setJSON($response);
        }

        $this->produtoModel->delete($id);

        $response = [
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Produto deletado com sucesso'
            ],
            'retorno' => $product
        ];

        return $this->response->setJSON($response);
    }
}
