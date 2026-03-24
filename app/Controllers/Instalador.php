<?php
namespace App\Controllers;
use CodeIgniter\Controller;

class Instalador extends Controller
{
    public function ejecutar()
    {
        $db = \Config\Database::connect();

        // Desactivar checks para poder limpiar
        $db->query("SET FOREIGN_KEY_CHECKS = 0;");
        
        // USA EL NOMBRE EXACTO: Modulo
        $db->table('Modulo')->truncate(); 
        $db->table('menu')->truncate();
        $db->table('permisos_perfil')->truncate();
        
        $db->query("SET FOREIGN_KEY_CHECKS = 1;");

        // Datos de los módulos
        $modulos = [
            ['id' => 1, 'strNombreModulo' => 'Perfil'],
            ['id' => 2, 'strNombreModulo' => 'Módulo'],
            ['id' => 3, 'strNombreModulo' => 'Permisos-Perfil'],
            ['id' => 4, 'strNombreModulo' => 'Usuario'],
            ['id' => 5, 'strNombreModulo' => 'Principal 1.1'],
            ['id' => 6, 'strNombreModulo' => 'Principal 1.2'],
            ['id' => 7, 'strNombreModulo' => 'Principal 2.1'],
            ['id' => 8, 'strNombreModulo' => 'Principal 2.2'],
        ];

        // INSERTAR EN LA TABLA CORRECTA
        $db->table('Modulo')->insertBatch($modulos);

        // ... El resto del código de insertar en 'menu' y 'permisos_perfil' se queda igual
        // porque esas tablas sí están en minúsculas según tu SQL anterior.
        
        // (Asegúrate de copiar el resto del código del paso anterior aquí abajo)
        
        return "Instalación completada en la tabla Modulo.";
    }
}