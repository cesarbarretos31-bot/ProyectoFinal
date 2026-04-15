<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class Auth extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }
        return view('login_view');
    }

    public function login()
    {
        $session = session();
        $db = \Config\Database::connect();

        // 1. Capturar datos del POST
        $postUsuario  = trim($this->request->getPost('usuario'));
        $postPassword = $this->request->getPost('password');

        // 2. Validación de entrada
        if (!$this->validate([
            'usuario' => 'required|alpha_numeric_space|min_length[3]|max_length[20]',
            'password' => 'required|min_length[6]|max_length[80]'
        ])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // 3. QUERY CON NOMBRES EXACTOS DE TU IMAGEN
        // Tabla: Usuario
        // Columna Nombre: strNombreUsuario
        $sql = "SELECT * FROM Usuario WHERE strNombreUsuario = ? LIMIT 1";
        $query = $db->query($sql, [$postUsuario]);
        $user = $query->getRow();

        // 3. VALIDACIÓN DE USUARIO
        if (!$user) {
            return $this->respond(['msg' => 'El usuario no existe'], 401);
        }

        // 4. VALIDACIÓN DE ESTADO
        if ($user->idEstado == 0) {
            return $this->respond(['msg' => 'Usuario inactivo'], 403);
        }

        // 5. VALIDACIÓN DE CONTRASEÑA
        // Columna Password: strPwd (según tu imagen)
        if (!password_verify($postPassword, $user->strPwd)) {
            return $this->respond(['msg' => 'Contraseña incorrecta'], 401);
        }

        // Si queremos forzar rehash cuando el algoritmo cambia:
        if (password_needs_rehash($user->strPwd, PASSWORD_BCRYPT)) {
            $db->table('Usuario')->where('id', $user->id)->update(['strPwd' => password_hash($postPassword, PASSWORD_BCRYPT)]);
        }

        // 6. CREAR SESIÓN
        $ses_data = [
            'idUsuario'    => $user->id,
            'nombre'       => $user->strNombreUsuario,
            'idPerfil'     => $user->idPerfil,
            'foto'         => base_url('uploads/' . ($user->strImagen ?? 'default.jpg')),
            'isLoggedIn'   => true
        ];
        $session->set($ses_data);

        return $this->respond([
            'status' => 200,
            'msg'    => '¡Bienvenido!',
            'redirect' => base_url('dashboard')
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }
}