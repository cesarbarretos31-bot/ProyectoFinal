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