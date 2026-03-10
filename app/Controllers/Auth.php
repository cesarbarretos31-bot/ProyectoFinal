<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Usuario_model'); // Lo crearemos a continuación
        $this->load->library('form_validation');
    }

    public function login() {
        // 1. Validar Captcha (Requisito obligatorio) [cite: 22]
        $captcha_user = $this->input->post('captcha');
        if (!$this->validate_captcha($captcha_user)) {
            return $this->output->set_status_header(401)->set_output(json_encode(['error' => 'Captcha incorrecto']));
        }

        // 2. Recibir credenciales [cite: 20]
        $usuario = $this->input->post('username');
        $password = $this->input->post('password');

        // 3. Consultar base de datos [cite: 20]
        $user_data = $this->Usuario_model->get_by_username($usuario);

        if ($user_data) {
            // Validar si está activo [cite: 21]
            if ($user_data->idEstado != 1) {
                return $this->output->set_status_header(401)->set_output(json_encode(['error' => 'Usuario inactivo']));
            }

            // Validar contraseña (usando password_verify para seguridad) [cite: 20]
            if (password_verify($password, $user_data->strPwd)) {
                
                // 4. Generar Token JWT [cite: 23]
                $key = "TU_LLAVE_SECRETA_SUPER_SEGURA";
                $payload = [
                    'iat' => time(), // Tiempo en que se creó
                    'exp' => time() + (60 * 60), // Expira en 1 hora
                    'uid' => $user_data->id,
                    'perfil' => $user_data->idPerfil
                ];

                $token = JWT::encode($payload, $key, 'HS256');

                echo json_encode([
                    'status' => 'success',
                    'token' => $token,
                    'user' => [
                        'nombre' => $user_data->strNombreUsuario,
                        'foto' => base_url('uploads/'.$user_data->strImagen) // [cite: 38]
                    ]
                ]);
            } else {
                echo json_encode(['error' => 'Contraseña incorrecta']);
            }
        } else {
            echo json_encode(['error' => 'El usuario no existe']); // [cite: 21]
        }
    }

    private function validate_captcha($input) {
        // Aquí implementarás la lógica de validación de tu captcha elegido
        return true; 
    }
}