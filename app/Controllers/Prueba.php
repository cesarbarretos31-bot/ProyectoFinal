<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Prueba extends Controller
{
    public function insertar()
{
    try {
        $db = \Config\Database::connect();
        echo "✅ Conectado correctamente";
    } catch (\Throwable $e) {
        echo "❌ Error: " . $e->getMessage();
    }
}
}