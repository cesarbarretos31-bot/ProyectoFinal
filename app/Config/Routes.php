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

// Módulo Perfil (¡OJO AQUÍ!)
$routes->get('perfil', 'Perfil::index');        // Retorna el JSON
$routes->get('perfil/vista', 'Perfil::vista');  // Retorna el HTML
$routes->post('perfil/crear', 'Perfil::crear');
$routes->delete('perfil/eliminar/(:num)', 'Perfil::eliminar/$1');