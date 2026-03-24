<?php
namespace App\Controllers;

class Principal extends BaseController {
    public function mostrar($nombre) {
        // Pasamos el nombre para el título y breadcrumb 
        return view('estatico_view', ['titulo' => ucfirst(str_replace('-', ' ', $nombre))]);
    }
}