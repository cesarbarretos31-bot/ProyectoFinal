<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Instalador extends Controller
{
    public function ejecutar()
    {
        $db = \Config\Database::connect();

        // 1. Limpiar tablas para evitar duplicados (Opcional, cuidado)
        $db->query("SET FOREIGN_KEY_CHECKS = 0;");
        $db->table('modulo')->truncate();
        $db->table('menu')->truncate();
        $db->table('permisos_perfil')->truncate();
        $db->query("SET FOREIGN_KEY_CHECKS = 1;");

        // 2. Definir los Módulos del PDF
        $modulos = [
            ['id' => 1, 'strNombreModulo' => 'Perfil'],
            ['id' => 2, 'strNombreModulo' => 'Módulo'],
            ['id' => 3, 'strNombreModulo' => 'Permisos-Perfil'],
            ['id' => 4, 'strNombreModulo' => 'Usuario'],
            ['id' => 5, 'strNombreModulo' => 'Principal 1.1'],
            ['id' => 6, 'strNombreModulo' => 'Principal 1.2'],
            ['id' => 7, 'strNombreModulo' => 'Principal 2.1'],
            ['id' => 8, 'strNombreModulo' => 'Principal 2.2'],
        ];

        $db->table('modulo')->insertBatch($modulos);

        // 3. Definir la jerarquía en la tabla Menu (idMenu es el "Padre")
        // idMenu 1 = Seguridad | 2 = Principal 1 | 3 = Principal 2
        $menuEstructura = [
            ['idMenu' => 1, 'idModulo' => 1], // Seguridad -> Perfil
            ['idMenu' => 1, 'idModulo' => 2], // Seguridad -> Módulo
            ['idMenu' => 1, 'idModulo' => 3], // Seguridad -> Permisos-Perfil
            ['idMenu' => 1, 'idModulo' => 4], // Seguridad -> Usuario
            ['idMenu' => 2, 'idModulo' => 5], // Principal 1 -> 1.1
            ['idMenu' => 2, 'idModulo' => 6], // Principal 1 -> 1.2
            ['idMenu' => 3, 'idModulo' => 7], // Principal 2 -> 2.1
            ['idMenu' => 3, 'idModulo' => 8], // Principal 2 -> 2.2
        ];

        $db->table('menu')->insertBatch($menuEstructura);

        // 4. Asignar todos los permisos al Perfil Administrador (idPerfil = 1)
        // Esto es para que en cuanto entres, ya veas todo el sidebar
        $permisosAdmin = [];
        foreach (range(1, 8) as $idModulo) {
            $permisosAdmin[] = [
                'idModulo'    => $idModulo,
                'idPerfil'    => 1, // ID de tu perfil admin
                'bitAgregar'  => 1,
                'bitEditar'   => 1,
                'bitConsulta' => 1,
                'bitEliminar' => 1,
                'bitDetalle'  => 1
            ];
        }

        $db->table('permisos_perfil')->insertBatch($permisosAdmin);

        return "<h3>Estructura del PDF instalada correctamente.</h3><p>Los módulos Principal 1 y 2 ya están ligados y con permisos para el Admin.</p>";
    }
}