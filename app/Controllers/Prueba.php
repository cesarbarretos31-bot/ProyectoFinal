<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Prueba extends Controller
{
    public function insertar()
    {
        $db = \Config\Database::connect();

        $data = [
            'nombre' => 'Cesar Test'
        ];

        $db->table('prueba')->insert($data);

        if ($db->affectedRows() > 0) {
            echo "✅ Insertado correctamente";
        } else {
            echo "❌ Error al insertar";
        }
    }
}