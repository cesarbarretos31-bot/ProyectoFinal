<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class Auth extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        return view('login_view');
    }

 public function login() {
    // PRUEBA DE LIBRERÍA: Si esto falla, el error 500 es por la instalación.
    if (!class_exists('\Firebase\JWT\JWT')) {
        return $this->respond(['error' => 'La librería Firebase JWT NO está instalada en el servidor'], 500);
    }

    try {
        // ... (Tu lógica de usuario y captcha)
        
        $key = "CLAVE_SECRETA_TEST";
        $payload = ['uid' => 1];
        $token = \Firebase\JWT\JWT::encode($payload, $key, 'HS256');

        return $this->respond([
            'status' => 200,
            'token' => $token,
            'msg' => '¡Librería detectada y funcionando!'
        ]);
    } catch (\Exception $e) {
        return $this->respond(['error' => $e->getMessage()], 500);
    }
}
}