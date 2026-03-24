<?php
namespace App\Filters;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('testdb', 'TestDB::index');
$routes->get('insertar-prueba', 'Prueba::insertar');
$routes->get('perfil', 'Perfil::index');         // Los datos JSON
$routes->get('perfil/vista', 'Perfil::vista');   // El HTML del módulo
$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->get('/', 'Auth::index');
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);
$routes->post('auth/login', 'Auth::login');
$routes->get('instalador/ejecutar', 'Instalador::ejecutar');    
$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('dashboard', 'Dashboard::index'); // O como se llame tu controlador de inicio
$routes->get('logout', 'Auth::logout');

// Rutas para los menús
$routes->get('menu/obtenerMenu', 'Menu::obtenerMenu');