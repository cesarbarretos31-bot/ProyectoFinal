<?php
class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Aquí es donde el AuthFilter que creamos entraría en acción [cite: 23, 24]
    }

    public function index() {
        echo "<h1>Bienvenido al Sistema Corporativo</h1>";
        echo "<p>Si puedes ver esto, tu sesión es válida.</p>";
        echo "<button onclick='logout()'>Cerrar Sesión</button>";
        
        // Script básico para probar que el token existe
        echo "<script>
            if(!localStorage.getItem('jwt_token')) {
                alert('No tienes token, volviendo al login...');
                window.location.href = '" . base_url('auth/login') . "';
            }
            function logout() {
                localStorage.removeItem('jwt_token');
                window.location.reload();
            }
        </script>";
    }
}