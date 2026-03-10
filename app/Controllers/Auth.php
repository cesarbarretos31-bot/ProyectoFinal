<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\API\ResponseTrait;

class Auth extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        return view('login_view');
    }

    public function login()
    {
        $session = session();
        $model = new UsuarioModel();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $model->obtenerUsuario($username);

        if ($user) {
            // Validar estado
            if ($user['idEstado'] != 1) {
                return $this->fail('El usuario no existe o está inactivo.', 401);
            }

            // Validar password
            if (password_verify($password, $user['strPwd'])) {
                
                $ses_data = [
                    'id'         => $user['id'],
                    'usuario'    => $user['strNombreUsuario'],
                    'idPerfil'   => $user['idPerfil'],
                    'isLoggedIn' => true
                ];
                
                $session->set($ses_data);

                return $this->respond([
                    'status' => 200,
                    'msg'    => 'Login exitoso',
                    'user'   => $user['strNombreUsuario']
                ], 200);

            } else {
                return $this->fail('Contraseña incorrecta.', 401);
            }
        } else {
            return $this->fail('El usuario no existe.', 401);
        }
    }
}