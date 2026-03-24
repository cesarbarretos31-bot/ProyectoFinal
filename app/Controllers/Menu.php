<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Menu extends ResourceController
{
    protected $format = 'json';

    public function obtenerMenu()
    {
        // 1. Recibimos el idPerfil desde el fetch del JS
        $idPerfil = $this->request->getGet('idPerfil');
        
        if (!$idPerfil) {
            return $this->fail('Falta el ID de Perfil', 400);
        }

        $db = \Config\Database::connect();

        // 2. QUERY MAESTRA: Traemos el grupo (idMenu) y el nombre del módulo
        // IMPORTANTE: Los nombres de las tablas coinciden con tu base de datos (PascalCase)
        $sql = "SELECT 
                    m.idMenu, 
                    modu.strNombreModulo,
                    p.bitConsulta,
                    p.bitAgregar,
                    p.bitEditar,
                    p.bitEliminar,
                    p.bitDetalle
                FROM Menu m
                JOIN Modulo modu ON m.idModulo = modu.id
                JOIN PermisosPerfil p ON p.idModulo = modu.id
                WHERE p.idPerfil = ? 
                  AND p.bitConsulta = 1
                ORDER BY m.idMenu ASC, modu.id ASC";

        try {
            $query = $db->query($sql, [$idPerfil]);
            $resultados = $query->getResultArray();

            // 3. Retornamos los datos al JS
            return $this->respond($resultados);
            
        } catch (\Exception $e) {
            return $this->failServerError('Error en la base de datos: ' . $e->getMessage());
        }
    }
}