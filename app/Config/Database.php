<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    public string $defaultGroup = 'default';

    public array $default = [
        'DSN'      => '',
        'hostname' => 'localhost',
        'username' => '',
        'password' => '',
        'database' => '',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => true,
        'charset'  => 'utf8mb4',
        'DBCollat' => 'utf8mb4_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 3306, // <--- Aquí solo pon el número fijo, sin funciones ni (int)
    ];

    public function __construct()
    {
        parent::__construct();

        // 🔥 Configuración para Railway
        // Solo sobrescribimos si existen las variables de entorno de Railway
        if (getenv('MYSQLHOST')) {
            $this->default['hostname'] = getenv('MYSQLHOST');
            $this->default['username'] = getenv('MYSQLUSER');
            $this->default['password'] = getenv('MYSQLPASSWORD');
            $this->default['database'] = getenv('MYSQLDATABASE');
            
            // ¡AQUÍ ES DONDE DEBES HACER LA CONVERSIÓN A ENTERO!
            $this->default['port'] = (int) (getenv('MYSQLPORT') ?: 3306);
        }
    }
}