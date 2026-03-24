<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Instalador extends Controller
{
    public function ejecutar()
    {
        $db = \Config\Database::connect();

        // 1. Desactivar checks para que nos deje borrar y meter datos
        $db->query("SET FOREIGN_KEY_CHECKS = 0;");
        
        // CORRECCIÓN DE NOMBRES EXACTOS SEGÚN TU BD
        $db->table('Modulo')->truncate(); 
        $db->table('Menu')->truncate();   
        $db->table('PermisosPerfil')->truncate(); // <-- Corregido con M mayúscula
        
        $db->query("SET FOREIGN_KEY_CHECKS = 1;");

        // 2. Insertar los Módulos del PDF
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

        // 3. Insertar la estructura del Menú (Jerarquía)
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

        // 4. Permisos para el Administrador (idPerfil = 1)
        // OJO: Si tu perfil de admin tiene otro ID, cámbialo aquí
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
        $db->table('PermisosPerfil')->insertBatch($permisosAdmin);

        return "<h1>¡A huevo!</h1><p>Tablas 'Modulo', 'Menu' y 'PermisosPerfil' llenas. Ya puedes probar el login.</p>";
    }
}