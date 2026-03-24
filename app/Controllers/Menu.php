<?php
namespace App\Controllers;
use CodeIgniter\API\ResponseTrait;

class Menu extends BaseController {
    use ResponseTrait;

    public function obtenerMenu() {
        $idPerfil = $this->request->getVar('idPerfil');
        $db = \Config\Database::connect();

        // Consulta que trae: El nombre del menú padre (Seguridad, Principal 1, etc) y el módulo
        $sql = "SELECT 
                    m.idMenu, 
                    modu.strNombreModulo, 
                    p.bitConsulta, p.bitAgregar, p.bitEditar, p.bitEliminar
                FROM menu m
                JOIN modulo modu ON m.idModulo = modu.id
                JOIN permisos_perfil p ON p.idModulo = modu.id
                WHERE p.idPerfil = ? AND p.bitConsulta = 1
                ORDER BY m.idMenu ASC";

        $query = $db->query($sql, [$idPerfil]);
        return $this->respond($query->getResultArray());
    }
}