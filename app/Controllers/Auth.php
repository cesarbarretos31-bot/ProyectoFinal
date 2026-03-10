<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;

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
        
        // 1. Validar reCaptcha de Google
        $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
        $secretKey = "6LfVYoYsAAAAALT4wql4uAmX68Gs2pASFoZHImE5"; // <--- PON TU SECRET KEY AQUÍ
        
        $client = \Config\Services::curlrequest();
        $resCaptcha = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret'   => $secretKey,
                'response' => $recaptchaResponse
            ]
        ]);

        $captchaResult = json_decode($resCaptcha->getBody());
        if (!$captchaResult->success) {
            return $this->fail(['error' => 'Por favor, completa el captcha correctamente.'], 401);
        }

        // 2. Validar Usuario
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $user = $model->obtenerUsuario($username);

        if ($user) {
            if ($user['idEstado'] != 1) {
                return $this->fail(['error' => 'El usuario está inactivo o no existe.'], 401);
            }

            if (password_verify($password, $user['strPwd'])) {
                
                // 3. Generar JWT
                $key = "TU_LLAVE_SECRETA_JWT"; // Inventa una palabra larga y segura
                $iat = time();
                $exp = $iat + 3600; // Expira en 1 hora

                $payload = [
                    "iss" => "Issuer del Proyecto",
                    "aud" => "Audience del Proyecto",
                    "iat" => $iat,
                    "exp" => $exp,
                    "uid" => $user['id'],
                    "perfil" => $user['idPerfil']
                ];

                $token = JWT::encode($payload, $key, 'HS256');

                return $this->respond([
                    'status' => 200,
                    'msg'    => 'Bienvenido al sistema',
                    'token'  => $token,
                    'user'   => [
                        'nombre' => $user['strNombreUsuario'],
                        'foto'   => $user['strImagen'] ? base_url('uploads/'.$user['strImagen']) : base_url('assets/default-user.png')
                    ]
                ], 200);

            }
        }
        
        return $this->fail(['error' => 'Usuario o contraseña incorrectos.'], 401);
    }
}