<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Instalador extends Controller
{
    public function ejecutar()
    {
        $db = \Config\Database::connect();

        // Desactivamos llaves foráneas para poder limpiar las tablas sin errores
        $db->query("SET FOREIGN_KEY_CHECKS = 0;");
        
        // NOMBRES EXACTOS DE TUS TABLAS
        $db->table('Modulo')->truncate(); 
        $db->table('Menu')->truncate();   
        $db->table('permisos_perfil')->truncate();
        
        $db->query("SET FOREIGN_KEY_CHECKS = 1;");

        // 1. Insertar en Modulo (con M mayúscula)
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
        $db->table('Modulo')->insertBatch($modulos);

        // 2. Insertar en Menu (con M mayúscula)
        // idMenu: 1=Seguridad, 2=Principal 1, 3=Principal 2
        $menuEstructura = [
            ['idMenu' => 1, 'idModulo' => 1],
            ['idMenu' => 1, 'idModulo' => 2],
            ['idMenu' => 1, 'idModulo' => 3],
            ['idMenu' => 1, 'idModulo' => 4],
            ['idMenu' => 2, 'idModulo' => 5],
            ['idMenu' => 2, 'idModulo' => 6],
            ['idMenu' => 3, 'idModulo' => 7],
            ['idMenu' => 3, 'idModulo' => 8],
        ];
        $db->table('Menu')->insertBatch($menuEstructura);

        // 3. Permisos para el Admin (idPerfil = 1)
        $permisosAdmin = [];
        foreach (range(1, 8) as $idModulo) {
            $permisosAdmin[] = [
                'idModulo'    => $idModulo,
                'idPerfil'    => 1, 
                'bitAgregar'  => 1,
                'bitEditar'   => 1,
                'bitConsulta' => 1,
                'bitEliminar' => 1,
                'bitDetalle'  => 1
            ];
        }
        $db->table('permisos_perfil')->insertBatch($permisosAdmin);

        return "<h1>¡Listo!</h1><p>Tablas 'Modulo' y 'Menu' actualizadas con la estructura del PDF.</p>";
    }
}