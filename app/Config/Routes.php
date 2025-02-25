<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Home::index');

$routes->get('/clientes', 'Cliente::index');
$routes->get('/clientes/(:num)', 'Cliente::show/$1');
$routes->post('/clientes', 'Cliente::create');
$routes->put('/clientes/(:num)', 'Cliente::update/$1');
$routes->delete('/clientes/(:num)', 'Cliente::delete/$1');

