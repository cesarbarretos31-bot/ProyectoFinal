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

        // 1. CAPTURAR DATOS
        $postUsuario  = $this->request->getPost('usuario'); 
        $postPassword = $this->request->getPost('password');

        // --- DIAGNÓSTICO 1: ¿Viene vacío? ---
        if (empty($postUsuario) || empty($postPassword)) {
            return $this->respond(['msg' => 'PHP no recibió nada. Revisa los nombres en el FormData.'], 401);
        }

        // 2. BUSCAR AL USUARIO
        $sql = "SELECT * FROM Usuario WHERE srtNombreUsuario = ? LIMIT 1";
        $query = $db->query($sql, [trim($postUsuario)]);
        $user = $query->getRow();

        // --- DIAGNÓSTICO 2: ¿Encontró la fila? ---
        if (!$user) {
            // Esto nos dirá si es un problema de mayúsculas o espacios
            return $this->respond(['msg' => "No existe: [$postUsuario] en la tabla Usuario."], 401);
        }

        // --- DIAGNÓSTICO 3: Estado ---
        if ($user->idEstado == 0) {
            return $this->respond(['msg' => 'Usuario inactivo.'], 403);
        }

        // --- DIAGNÓSTICO 4: La contraseña ---
        // Usamos trim() por si la base de datos tiene espacios invisibles
        if (trim($user->srtPwd) !== trim($postPassword)) {
            return $this->respond([
                'msg' => "Pass mal. BD: [" . $user->srtPwd . "] vs Recibido: [" . $postPassword . "]"
            ], 401);
        }

        // 3. SI TODO PASÓ, CREAR SESIÓN
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