<?php

namespace App\Controllers;

class Instalador extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        echo "<h2>Iniciando instalación en CI4...</h2>";

        // 1. Perfil [cite: 26, 27]
        $db->query("CREATE TABLE IF NOT EXISTS Perfil (
            id INT AUTO_INCREMENT PRIMARY KEY,
            strNombrePerfil VARCHAR(100) NOT NULL,
            bitAdministrador TINYINT(1) DEFAULT 0
        ) ENGINE=InnoDB;");

        // 2. Modulo [cite: 31, 32]
        $db->query("CREATE TABLE IF NOT EXISTS Modulo (
            id INT AUTO_INCREMENT PRIMARY KEY,
            strNombreModulo VARCHAR(100) NOT NULL
        ) ENGINE=InnoDB;");

        // 3. Usuario [cite: 28, 29, 30, 38]
        $db->query("CREATE TABLE IF NOT EXISTS Usuario (
            id INT AUTO_INCREMENT PRIMARY KEY,
            strNombreUsuario VARCHAR(100) NOT NULL,
            idPerfil INT,
            strPwd VARCHAR(255) NOT NULL,
            idEstado INT,
            strCorreo VARCHAR(150),
            strNumeroCelular VARCHAR(20),
            strImagen VARCHAR(255),
            FOREIGN KEY (idPerfil) REFERENCES Perfil(id)
        ) ENGINE=InnoDB;");

        // 4. Permisos Perfil [cite: 33, 34]
        $db->query("CREATE TABLE IF NOT EXISTS PermisosPerfil (
            id INT AUTO_INCREMENT PRIMARY KEY,
            idModulo INT,
            idPerfil INT,
            bitAgregar TINYINT(1) DEFAULT 0,
            bitEditar TINYINT(1) DEFAULT 0,
            bitConsulta TINYINT(1) DEFAULT 0,
            bitEliminar TINYINT(1) DEFAULT 0,
            bitDetalle TINYINT(1) DEFAULT 0,
            FOREIGN KEY (idModulo) REFERENCES Modulo(id),
            FOREIGN KEY (idPerfil) REFERENCES Perfil(id)
        ) ENGINE=InnoDB;");

        // 5. Menu [cite: 35]
        $db->query("CREATE TABLE IF NOT EXISTS Menu (
            id INT AUTO_INCREMENT PRIMARY KEY,
            idMenu INT,
            idModulo INT,
            FOREIGN KEY (idModulo) REFERENCES Modulo(id)
        ) ENGINE=InnoDB;");

        $this->seed($db);
        echo "<h3>¡Tablas creadas y Usuario admin (pass: admin123) listo!</h3>";
    }

    private function seed($db)
    {
        $db->query("INSERT IGNORE INTO Perfil (id, strNombrePerfil, bitAdministrador) VALUES (1, 'Administrador', 1);");
        $pass = password_hash("admin123", PASSWORD_BCRYPT);
        $db->query("INSERT IGNORE INTO Usuario (strNombreUsuario, idPerfil, strPwd, idEstado) VALUES ('admin', 1, '$pass', 1);");
    }
}