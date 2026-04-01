<?php
namespace App\Models;
use CodeIgniter\Model;

class PermisosModel extends Model {
    protected $table = 'PermisosPerfil'; // Nombre según tu BD
    protected $primaryKey = 'id';
    protected $allowedFields = ['idModulo', 'idPerfil', 'bitAgregar', 'bitEditar', 'bitConsulta', 'bitEliminar', 'bitDetalle'];

    public function getPermisosUsuario($idPerfil) {
        return $this->db->table('PermisosPerfil as p')
            ->select('m.strNombreModulo, m.id as idModulo, p.bitConsulta, p.bitAgregar, p.bitEditar, p.bitEliminar, p.bitDetalle')
            ->join('Modulo as m', 'm.id = p.idModulo')
            ->where('p.idPerfil', $idPerfil)
            ->where('(p.bitConsulta = 1 OR p.bitAgregar = 1 OR p.bitEditar = 1 OR p.bitEliminar = 1 OR p.bitDetalle = 1)')
            ->get()->getResultArray();
    }
}