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

        } catch (\Throwable $e) {
            echo "❌ Error: " . $e->getMessage();
        }
    }
}
