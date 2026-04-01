<?php
namespace App\Models;
use CodeIgniter\Model;

class UsuarioModel extends Model {
    protected $table = 'Usuario';
    protected $primaryKey = 'id';
    // Campos requeridos por el PDF [cite: 29, 30]
    protected $allowedFields = [
        'strNombreUsuario', 'idPerfil', 'strPwd', 
        'idEstado', 'strCorreo', 'strNumeroCelular', 'strImagen'
    ];
}