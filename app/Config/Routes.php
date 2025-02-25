<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Home::index');

$routes->get('/clientes', 'Cliente::index');
$routes->get('/clientes/(:num)', 'Cliente::show/$1');
$routes->post('/clientes', 'Cliente::create');
$routes->put('/clientes/(:num)', 'Cliente::update/$1');
$routes->delete('/clientes/(:num)', 'Cliente::delete/$1');

$routes->get('/produtos', 'Produto::index');
$routes->get('/produtos/(:num)', 'Produto::show/$1');
$routes->post('/produtos', 'Produto::create');
$routes->put('/produtos/(:num)', 'Produto::update/$1');
$routes->delete('/produtos/(:num)', 'Produto::delete/$1');

$routes->get('/pedidos', 'Pedido::index');
$routes->get('/pedidos/(:num)', 'Pedido::show/$1');
$routes->post('/pedidos', 'Pedido::create');
$routes->put('/pedidos/(:num)', 'Pedido::update/$1');
$routes->delete('/pedidos/(:num)', 'Pedido::delete/$1');