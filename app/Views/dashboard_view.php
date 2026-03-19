<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Sistema Corporativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; }
        #sidebar { min-width: 250px; max-width: 250px; background: #2c3e50; color: white; min-height: 100vh; transition: all 0.3s; }
        #sidebar .nav-link { color: rgba(255,255,255,0.7); border-radius: 5px; margin: 5px 10px; }
        #sidebar .nav-link:hover, #sidebar .nav-link.active { background: #34495e; color: white; }
        .main-content { width: 100%; }
        .top-navbar { background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.08); }
    </style>
</head>
<body>

<div class="d-flex">
    <nav id="sidebar">
        <div class="p-4">
            <h4 class="text-center">Mi Empresa</h4>
        </div>
        <ul class="nav flex-column" id="menuDinamico">
            </ul>
        <hr>
        <div class="px-3">
            <button onclick="logout()" class="btn btn-outline-light btn-sm w-100">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </button>
        </div>
    </nav>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg top-navbar px-4 py-3">
            <div class="container-fluid">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" id="breadcrumbArea">
                        <li class="breadcrumb-item active">Inicio</li>
                    </ol>
                </nav>
                <div class="ms-auto d-flex align-items-center">
                    <span id="userNameDisplay" class="me-2 fw-semibold"></span>
                    <img id="userImgDisplay" src="" class="rounded-circle" width="35" height="35">
                </div>
            </div>
        </nav>

        <div class="container-fluid p-4" id="mainWrapper">
            <div class="card p-5 text-center">
                <h2>¡Bienvenido al Sistema!</h2>
                <p>Selecciona un módulo del menú para comenzar.</p>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Verificar si hay token al entrar
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user'));

    if (!token) {
        window.location.href = '<?= base_url("login") ?>';
    }

    // 2. Mostrar datos del usuario
    document.getElementById('userNameDisplay').innerText = user.nombre;
    document.getElementById('userImgDisplay').src = user.foto;

    function logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '<?= base_url("login") ?>';
    }

    // Aquí insertaremos la función para cargar el menú dinámico después
</script>
</body>
</html>