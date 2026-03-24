<?php
namespace App\Filters;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('testdb', 'TestDB::index');
$routes->get('insertar-prueba', 'Prueba::insertar');

$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->get('/', 'Auth::index');
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);
$routes->post('auth/login', 'Auth::login');
$routes->get('instalador/ejecutar', 'Instalador::ejecutar');    