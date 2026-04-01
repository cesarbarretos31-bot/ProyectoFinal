<?php
namespace App\Controllers;

class Principal extends BaseController {
    public function mostrar($nombre) {
        // Convertir el sufijo 1-2/2-1 etc en un título legible
        $titulo = 'Principal ' . str_replace('-', '.', $nombre);

        // Obtener permisos específicos de este módulo
        $permisos = $this->getPermisosModulo($titulo);

        return view('estatico_view', ['titulo' => $titulo, 'permisos' => $permisos]);
    }
}