<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class Auth extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        // Si ya hay sesión, directo al dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }
        return view('login_view');
    }

    public function login()
    {
        $session = session();
        $db = \Config\Database::connect();

        // 1. Recibir datos del formulario (POST)
        $postUsuario  = $this->request->getPost('usuario'); 
        $postPassword = $this->request->getPost('password');

        // 2. QUERY CON TUS NOMBRES EXACTOS
        // Tabla: Usuario | Columna: srtNombreUsuario
        $sql = "SELECT * FROM Usuario WHERE srtNombreUsuario = ? LIMIT 1";
        $query = $db->query($sql, [$postUsuario]);
        $user = $query->getRow();

        // 3. VALIDACIÓN DE EXISTENCIA
        if (!$user) {
            return $this->respond(['msg' => 'El usuario no existe'], 401);
        }

        // 4. VALIDACIÓN DE ESTADO (Punto 21 del PDF)
        if ($user->idEstado == 0) {
            return $this->respond(['msg' => 'Usuario inactivo. Contacte al administrador.'], 403);
        }

        // 5. VALIDACIÓN DE CONTRASEÑA (Columna: srtPwd)
        // Usamos comparación directa (password_verify es solo si están hasheadas)
        if ($user->srtPwd !== $postPassword) {
            return $this->respond(['msg' => 'Contraseña incorrecta'], 401);
        }

        // 6. CREAR SESIÓN CON TUS COLUMNAS
        $ses_data = [
            'idUsuario'    => $user->id,
            'nombre'       => $user->srtNombreUsuario,
            'idPerfil'     => $user->idPerfil,
            'foto'         => base_url('uploads/' . ($user->srtImagen ?? 'default.png')),
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