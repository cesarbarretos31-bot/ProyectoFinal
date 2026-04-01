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

// Rutas para los módulos CRUD
$routes->get('modulo/vista', '\App\Controllers\Modulo::vista');
$routes->get('usuario/vista', '\App\Controllers\Usuarios::vista');
$routes->get('permisos-perfil/vista', '\App\Controllers\PermisosPerfil::vista');

// Rutas para módulos estáticos (Principal 1.1 / 1.2 / 2.1 / 2.2)
$routes->get('principal-1-1/vista', '\App\Controllers\Principal::mostrar/1-1');
$routes->get('principal-1-2/vista', '\App\Controllers\Principal::mostrar/1-2');
$routes->get('principal-2-1/vista', '\App\Controllers\Principal::mostrar/2-1');
$routes->get('principal-2-2/vista', '\App\Controllers\Principal::mostrar/2-2');

// API REST de Perfil (CRUD)
$routes->get('perfil/listar', '\App\Controllers\Perfil::listar');
$routes->get('perfil/obtener/(:num)', '\App\Controllers\Perfil::obtener/$1');
$routes->post('perfil/guardar', '\App\Controllers\Perfil::guardar');
$routes->post('perfil/eliminar/(:num)', '\App\Controllers\Perfil::eliminar/$1');

// API REST de Módulo
$routes->get('modulo/listar', '\App\Controllers\Modulo::listar');
$routes->get('modulo/obtener/(:num)', '\App\Controllers\Modulo::obtener/$1');
$routes->post('modulo/guardar', '\App\Controllers\Modulo::guardar');
$routes->post('modulo/eliminar/(:num)', '\App\Controllers\Modulo::eliminar/$1');

// API REST de Usuario
$routes->get('usuario/listar', '\App\Controllers\Usuarios::listar');
$routes->get('usuario/obtener/(:num)', '\App\Controllers\Usuarios::obtener/$1');
$routes->post('usuario/guardar', '\App\Controllers\Usuarios::guardar');
$routes->post('usuario/eliminar/(:num)', '\App\Controllers\Usuarios::eliminar/$1');

// API REST de Permisos Perfil
$routes->get('permisosperfil/listar', '\App\Controllers\PermisosPerfil::listar');
$routes->get('permisosperfil/mostrar/(:num)', '\App\Controllers\PermisosPerfil::mostrar/$1');
$routes->post('permisosperfil/guardar', '\App\Controllers\PermisosPerfil::guardar');
$routes->post('permisosperfil/actualizar', '\App\Controllers\PermisosPerfil::actualizar');
$routes->post('permisosperfil/eliminar/(:num)', '\App\Controllers\PermisosPerfil::eliminar/$1');