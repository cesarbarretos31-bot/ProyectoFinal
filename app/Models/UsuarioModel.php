<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table      = 'Usuario';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'strNombreUsuario', 'idPerfil', 'strPwd', 
        'idEstado', 'strCorreo', 'strNumeroCelular', 'strImagen'
    ]; // Campos definidos en el documento [cite: 29, 30, 38]

    // Función para buscar al usuario en el Login
    public function obtenerUsuario($username)
    {
        return $this->where('strNombreUsuario', $username)->first();
    }
}