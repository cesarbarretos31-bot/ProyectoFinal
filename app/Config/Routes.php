<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('testdb', 'TestDB::index');
$routes->get('insertar-prueba', 'Prueba::insertar');
// Ruta por defecto (el Login)
$route['default_controller'] = 'auth';

// Rutas de Autenticación
$route['login'] = 'auth/index';          // Carga la vista del login
$route['auth/login'] = 'auth/login';    // Procesa el formulario (POST)

// Rutas del Dashboard y Módulos
$route['dashboard'] = 'dashboard/index';

// Ejemplo para el CRUD de Perfiles (luego crearemos este controlador)
$route['perfil/listar'] = 'perfil/get_all';
$route['perfil/crear'] = 'perfil/insert';

// Ruta de Error Personalizada (Requisito del proyecto)