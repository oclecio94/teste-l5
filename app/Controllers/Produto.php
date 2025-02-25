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
        $produtos = $this->produtoModel->findAll();
    
        $response = [
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Dados retornados com sucesso'
            ],
            'retorno' => $produtos
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

        // Verifica se o produto já existe (pode usar o nome ou outro critério, como código de produto)
        $existingProduct = $this->produtoModel->where('nome', $request['nome'])->first();

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

        $data = $this->produtoModel->insert($request);

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

        $existingProduct = $this->produtoModel->where('nome', $request['nome'])->where('id !=', $id)->first();

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

        $this->produtoModel->update($id, $request);

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
