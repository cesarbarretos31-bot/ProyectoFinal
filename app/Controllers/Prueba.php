<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Prueba extends Controller
{
public function install_db() {
    $this->load->database();

    // 1. Tabla Perfil [cite: 27]
    $this->db->query("CREATE TABLE IF NOT EXISTS Perfil (
        id INT AUTO_INCREMENT PRIMARY KEY,
        strNombrePerfil VARCHAR(100) NOT NULL,
        bitAdministrador TINYINT(1) DEFAULT 0
    ) ENGINE=InnoDB;");

    // 2. Tabla Modulo [cite: 32]
    $this->db->query("CREATE TABLE IF NOT EXISTS Modulo (
        id INT AUTO_INCREMENT PRIMARY KEY,
        strNombreModulo VARCHAR(100) NOT NULL
    ) ENGINE=InnoDB;");

    // 3. Tabla Usuario [cite: 29, 30, 38]
    $this->db->query("CREATE TABLE IF NOT EXISTS Usuario (
        id INT AUTO_INCREMENT PRIMARY KEY,
        strNombreUsuario VARCHAR(100) NOT NULL,
        idPerfil INT,
        strPwd VARCHAR(255) NOT NULL,
        idEstado INT COMMENT '1:Activo, 0:Inactivo',
        strCorreo VARCHAR(150),
        strNumeroCelular VARCHAR(20),
        strImagen VARCHAR(255),
        FOREIGN KEY (idPerfil) REFERENCES Perfil(id)
    ) ENGINE=InnoDB;");

    // 4. Tabla PermisosPerfil [cite: 34]
    $this->db->query("CREATE TABLE IF NOT EXISTS PermisosPerfil (
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

    // 5. Tabla Menu (Para enlazar módulos con el menú) [cite: 35]
    $this->db->query("CREATE TABLE IF NOT EXISTS Menu (
        id INT AUTO_INCREMENT PRIMARY KEY,
        idMenu INT COMMENT 'Identificador del grupo de menú',
        idModulo INT,
        FOREIGN KEY (idModulo) REFERENCES Modulo(id)
    ) ENGINE=InnoDB;");

    echo "¡Base de datos creada exitosamente en Railway!";
}
}

