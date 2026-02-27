<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Prueba extends Controller
{
   
public function insertar()
{
    try {
        $db = \Config\Database::connect();

        $data = [
            'nombre' => 'Prueba Railway'
        ];

        $insert = $db->table('prueba')->insert($data);

        if ($insert) {
            echo "✅ Insertado correctamente";
        } else {
            echo "❌ No se insertó";
            print_r($db->error());
        }

    } catch (\Throwable $e) {
        echo "❌ Error: " . $e->getMessage();
    }
}

}