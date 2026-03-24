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
        // Si ya tiene sesión, mandarlo directo al dashboard
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
        $usuario  = $this->request->getPost('usuario');
        $password = $this->request->getPost('password');

        // 2. Buscar al usuario en la base de datos con su Perfil
        // Usamos tus nombres de tablas: Usuarios y Perfiles
        $sql = "SELECT u.*, p.strNombrePerfil, p.bitAdministrador 
                FROM Usuarios u 
                JOIN Perfiles p ON u.idPerfil = p.id 
                WHERE u.strNombreUsuario = ? LIMIT 1";
        
        $query = $db->query($sql, [$usuario]);
        $user = $query->getRow();

        // 3. Validaciones de seguridad
        if (!$user) {
            return $this->respond(['msg' => 'Usuario no encontrado'], 401);
        }

        if ($user->idEstado == 0) { // Punto 21 del PDF: Usuario Inactivo
            return $this->respond(['msg' => 'Usuario inactivo. Contacte al admin.'], 403);
        }

        // 4. Verificar Password (asumiendo que usas password_hash)
        if (!password_verify($password, $user->strPwd)) {
            // Nota: Si en tu BD la clave es texto plano (solo para pruebas), usa: if($password != $user->strPwd)
            return $this->respond(['msg' => 'Contraseña incorrecta'], 401);
        }

        // 5. Generar JWT (Opcional si usas Session, pero el PDF lo pide)
        $key = "ESTA_ES_UNA_LLAVE_SUPER_SECRETA_12345";
        $payload = [
            'uid' => $user->id,
            'iat' => time(),
            'exp' => time() + 3600
        ];
        $token = JWT::encode($payload, $key, 'HS256');

        // 6. CREAR LA SESIÓN EN EL SERVIDOR (Esto es lo que pediste)
        $ses_data = [
            'idUsuario'    => $user->id,
            'nombre'       => $user->strNombreUsuario,
            'idPerfil'     => $user->idPerfil,
            'foto'         => base_url('uploads/' . $user->strImagen),
            'isAdmin'      => $user->bitAdministrador,
            'isLoggedIn'   => true,
            'token'        => $token // Guardamos el token en la sesión también
        ];
        $session->set($ses_data);

        return $this->respond([
            'status' => 200,
            'msg'    => 'Login exitoso',
            'redirect' => base_url('dashboard')
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }
}