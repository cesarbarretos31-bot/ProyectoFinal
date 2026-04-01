<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuloModel extends Model
{
    protected $table = 'Modulo';
    protected $primaryKey = 'id';
    protected $allowedFields = ['strNombreModulo'];
}