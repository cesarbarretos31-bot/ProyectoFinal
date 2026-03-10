<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Prueba extends Controller
{
public function insertar()
{
    try {
        $db = \Config\Database::connect();
        $data = ['nombre' => 'Prueba desde Cero'];

        if ($db->table('prueba')->insert($data)) {
            echo "✅ ¡ÉXITO! Datos insertados correctamente.";
        } else {
            echo "❌ Error en el insert: ";
            print_r($db->error());
        }
    } catch (\Throwable $e) {
        echo "❌ Error de conexión o ejecución: " . $e->getMessage();
    }
}
}
