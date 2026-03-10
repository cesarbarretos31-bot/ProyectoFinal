<?php

namespace App\Models;

use CodeIgniter\Model;

class PermisosModel extends Model
{
    protected $table      = 'PermisosPerfil';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'idModulo', 'idPerfil', 'bitAgregar', 'bitEditar', 
        'bitConsulta', 'bitEliminar', 'bitDetalle'
    ];
}