<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TestDB extends Controller
{
    public function index()
    {
        try {
            // Conectar a la base de datos
            $db = \Config\Database::connect();
            echo "✅ Conectado correctamente a la base de datos<br>";

            // Ejecutar queries para arreglar la tabla
            $db->query("ALTER TABLE usuarios MODIFY id INT(11) NOT NULL AUTO_INCREMENT");
            $db->query("ALTER TABLE usuarios AUTO_INCREMENT = 1");

            echo "✅ Tabla 'usuarios' arreglada correctamente!";
        } catch (\Throwable $e) {
            echo "❌ Error: " . $e->getMessage();
        }
    }
}
