<?php

namespace App\Models;

use CodeIgniter\Model;

class PerfilModel extends Model
{
    protected $table      = 'Perfil';
    protected $primaryKey = 'id';
    protected $allowedFields = ['strNombrePerfil', 'bitAdministrador'];
}