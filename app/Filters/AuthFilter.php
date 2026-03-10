<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthFilter {
    protected $key = "TU_LLAVE_SECRETA_SUPER_SEGURA";

    public function before() {
        // 1. Obtener el token del Header 'Authorization'
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
        if (!$authHeader) {
            // Si no hay token, redirigir al login 
            redirect('auth/login');
            exit;
        }

        try {
            // 2. Limpiar el string 'Bearer ' y decodificar
            $token = str_replace('Bearer ', '', $authHeader);
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
            
            // Guardar datos del usuario en la sesión temporal para usar en los CRUDs
            $this->CI =& get_instance();
            $this->CI->session->set_userdata('user_id', $decoded->uid);
            $this->CI->session->set_userdata('perfil_id', $decoded->perfil);

        } catch (Exception $e) {
            // Token inválido o expirado
            redirect('auth/login');
            exit;
        }
    }
}