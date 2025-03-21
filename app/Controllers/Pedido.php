<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class Pedido extends ResourceController
{
    private $pedidoModel;
    private $pedidoProdutoModel;
    private $clienteModel;
    private $produtoModel;

    public function __construct()
    {

        $this->pedidoModel = new \App\Models\PedidoModel();
        $this->pedidoProdutoModel = new \App\Models\PedidoProdutoModel();
        $this->clienteModel = new \App\Models\ClienteModel();
        $this->produtoModel = new \App\Models\ProdutoModel();
    }

    public function index()
    {
        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 10;
        $clienteId = $this->request->getGet('cliente_id');
        $status = $this->request->getGet('status');

        $query = $this->pedidoModel;

        if ($clienteId) {
            $query = $query->where('cliente_id', $clienteId);
        }

        if ($status) {
            $query = $query->where('status', $status);
        }

        $pedidos = $query->paginate($perPage, 'default', $page);
        $pager = $query->pager;

        foreach ($pedidos as &$pedido) {
            $pedido['produtos'] = $this->pedidoProdutoModel
                ->select('pedidos_produtos.produto_id, produtos.nome, pedidos_produtos.quantidade, pedidos_produtos.preco_unitario')
                ->join('produtos', 'produtos.id = pedidos_produtos.produto_id')
                ->where('pedido_id', $pedido['id'])
                ->findAll();
        }

        $response = [
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Dados retornados com sucesso'
            ],
            'retorno' => [
                'dados' => $pedidos,
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
        $pedido = $this->pedidoModel->find($id);
        if (!$pedido) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Pedido não encontrado'
                ],
                'retorno' => null
            ]);
        }

        $produtos = $this->pedidoProdutoModel
            ->select('pedidos_produtos.produto_id, produtos.nome, pedidos_produtos.quantidade, pedidos_produtos.preco_unitario')
            ->join('produtos', 'produtos.id = pedidos_produtos.produto_id')
            ->where('pedido_id', $pedido['id'])
            ->findAll();

        $pedido['produtos'] = $produtos;

        return $this->response->setJSON([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Dados retornados com sucesso'
            ],
            'retorno' => $pedido
        ]);
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

        $existingClient = $this->clienteModel->where('id', $parametros['cliente_id'])->first();

        if (!$existingClient) {
            $response = [
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'Cliente não encontrado'
                ],
                'retorno' => null
            ];
            return $this->response->setJSON($response);
        }


        foreach ($parametros['produtos'] as $produto) {
            $produtoAtual = $this->produtoModel->find($produto['produto_id']);

            if (!$produtoAtual) {
                return $this->response->setJSON([
                    'cabecalho' => ['status' => 404, 'mensagem' => 'Produto ID ' . $produto['produto_id'] . ' não encontrado'],
                    'retorno' => null
                ]);
            }

            if ($produtoAtual['quantidade'] < $produto['quantidade']) {
                return $this->response->setJSON([
                    'cabecalho' => [
                        'status' => 400,
                        'mensagem' => 'Estoque insuficiente para o produto: ' . $produtoAtual['nome']
                    ],
                    'retorno' => null
                ]);
            }
        }

        $data = $this->pedidoModel->insert($parametros);

        $pedidoId = $this->pedidoModel->getInsertID();

        if (!$pedidoId) {
            $response = [
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Erro ao criar pedido'
                ],
                'retorno' => null
            ];
            return $this->response->setJSON($response);
        }

        foreach ($parametros['produtos'] as $produto) {
            $pedidoProdutoData = [
                'pedido_id' => $pedidoId,
                'produto_id' => $produto['produto_id'],
                'quantidade' => $produto['quantidade'],
                'preco_unitario' => $produto['preco_unitario']
            ];

            $this->pedidoProdutoModel->insert($pedidoProdutoData);

            $novaQuantidade = $produtoAtual['quantidade'] - $produto['quantidade'];

            $this->produtoModel->update($produto['produto_id'], ['quantidade' => $novaQuantidade]);
        }

        $pedido = $this->pedidoModel->find($pedidoId);
        $produtos = $this->pedidoProdutoModel
            ->select('produto_id, quantidade, preco_unitario')
            ->where('pedido_id', $pedidoId)
            ->findAll();
        $pedido['produtos'] = $produtos;

        $response = [
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Pedido criado com sucesso'
            ],
            'retorno' => $pedido
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

        $pedido = $this->pedidoModel->find($id);

        if (!$pedido) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Pedido não encontrado'
                ],
                'retorno' => null
            ]);
        }

        if (!isset($parametros['status'])) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'O campo status é obrigatório'
                ],
                'retorno' => null
            ]);
        }

        $this->pedidoModel->update($id, ['status' => $parametros['status']]);

        $updatedPedido = $this->pedidoModel->find($id);

        return $this->response->setJSON([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Status do pedido atualizado com sucesso'
            ],
            'retorno' => $updatedPedido
        ]);
    }

    public function delete($id = null)
    {
        $pedido = $this->pedidoModel->find($id);
        if (!$pedido) {
            $response = [
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Pedido não encontrado'
                ],
                'retorno' => null
            ];

            return $this->response->setJSON($response);
        }

        $this->pedidoProdutoModel->where('pedido_id', $id)->delete();

        $this->pedidoModel->delete($id);

        $response = [
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Pedido deletado com sucesso'
            ],
            'retorno' => $pedido
        ];

        return $this->response->setJSON($response);
    }
}
