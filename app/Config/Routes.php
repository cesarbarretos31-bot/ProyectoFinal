<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('testdb', 'TestDB::index');
$routes->get('insertar-prueba', 'Prueba::insertar');
