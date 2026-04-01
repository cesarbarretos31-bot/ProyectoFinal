<?php
namespace App\Filters;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Rutas de Autenticación
$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// Dashboard y Menú
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);
$routes->get('menu/obtenerMenu', 'Menu::obtenerMenu');



// Ruta para cargar la vista del módulo perfil
$routes->get('perfil/vista', '\App\Controllers\Perfil::vista');

// API REST de Perfil (CRUD)
$routes->get('perfil/listar', '\App\Controllers\Perfil::listar');
$routes->get('perfil/obtener/(:num)', '\App\Controllers\Perfil::obtener/$1');
$routes->post('perfil', '\App\Controllers\Perfil::crear');
$routes->put('perfil/(:num)', '\App\Controllers\Perfil::actualizar/$1');
$routes->delete('perfil/(:num)', '\App\Controllers\Perfil::eliminar/$1');