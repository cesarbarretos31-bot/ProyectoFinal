<?php

namespace App\Controllers;

class Perfil extends BaseController
{
    // Este es el método que tu Dashboard está intentando cargar
    public function vista()
    {
        // Le decimos que devuelva la vista de perfil que crearemos en el paso 2
        return view('perfil_vista'); 
    }
}