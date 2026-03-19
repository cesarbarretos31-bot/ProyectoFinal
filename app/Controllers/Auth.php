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

   public function login()
{
    $model = new UsuarioModel();
    
    $username = $this->request->getPost('username');
    $password = $this->request->getPost('password');

    // BUSCAMOS AL USUARIO
    $user = $model->where('strNombreUsuario', $username)->first();

    if (!$user) {
        return $this->fail(['error' => 'El usuario no existe en la DB'], 401);
    }

    if ($user['idEstado'] != 1) {
        return $this->fail(['error' => 'Usuario inactivo'], 401);
    }

    // PRUEBA DE FUEGO: ¿La contraseña coincide?
    if (password_verify($password, $user['strPwd'])) {
        
        // Generar JWT
      //  $key = "TU_LLAVE_SECRETA_JWT";
        $iat = time();
        $payload = [
            "iat" => $iat,
            "exp" => $iat + 3600,
            "uid" => $user['id'],
            "perfil" => $user['idPerfil']
        ];

       // $token = JWT::encode($payload, $key, 'HS256');

        return $this->respond([
            'status' => 200,
          //  'token'  => $token,
            'user'   => [
                'nombre' => $user['strNombreUsuario'],
                'foto'   => $user['strImagen'] ? base_url('uploads/'.$user['strImagen']) : ''
            ]
        ], 200);
    } else {
        // Si entra aquí, es que 'admin123' NO coincide con lo que hay en la DB
        return $this->fail(['error' => 'Password incorrecto en password_verify'], 401);
    }
}
}