<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Sistema Corporativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #f4f7f6; }
        #sidebar { min-width: 250px; max-width: 250px; background: #2c3e50; color: white; min-height: 100vh; transition: all 0.3s; }
        #sidebar .nav-link { color: rgba(255,255,255,0.7); border-radius: 5px; margin: 5px 10px; cursor: pointer; }
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
        <ul class="nav flex-column" id="sidebar-dinamico">
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
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                    </ol>
                </nav>
                <div class="ms-auto d-flex align-items-center">
                    <span id="userNameDisplay" class="me-2 fw-semibold"></span>
                    <img id="userImgDisplay" src="" class="rounded-circle" width="35" height="35" onerror="this.src='https://via.placeholder.com/35'">
                </div>
            </div>
        </nav>

        <div class="container-fluid p-4" id="mainWrapper">
            <div class="card p-5 text-center shadow-sm">
                <h2>¡Bienvenido al Sistema!</h2>
                <p>Selecciona un módulo del menú para comenzar.</p>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. VALIDACIÓN DE SESIÓN INMEDIATA 
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user'));

    if (!token || !user) {
        window.location.href = '<?= base_url("login") ?>';
    }

    // 2. MOSTRAR DATOS DEL USUARIO
    document.getElementById('userNameDisplay').innerText = user.nombre;
    document.getElementById('userImgDisplay').src = user.foto;

    // 3. CARGA DEL MENÚ AL INICIAR
    document.addEventListener('DOMContentLoaded', () => {
        renderizarMenu();
    });

   async function renderizarMenuCompleto() {
    const user = JSON.parse(localStorage.getItem('user'));
    const token = localStorage.getItem('token');
    const sidebar = document.getElementById('sidebar-dinamico');

    const response = await fetch(`<?= base_url('menu/obtenerMenu') ?>?idPerfil=${user.idPerfil}`, {
        headers: { 'Authorization': `Bearer ${token}` }
    });
    
    const datos = await response.json();
    sidebar.innerHTML = ''; 

    // Nombres de los menús según el ID en la tabla 'menu'
    const nombresMenus = {
        1: 'SEGURIDAD',
        2: 'PRINCIPAL 1',
        3: 'PRINCIPAL 2'
    };

    let currentMenuId = null;

    datos.forEach(p => {
        // Si cambiamos de grupo (ej: de Seguridad a Principal 1), ponemos un título
        if (p.idMenu !== currentMenuId) {
            currentMenuId = p.idMenu;
            sidebar.innerHTML += `
                <li class="nav-small-cap mt-3 mb-1 px-3">
                    <span class="text-uppercase fw-bold shadow-sm" style="font-size: 0.75rem; color: #8e9aaf;">
                        ${nombresMenus[p.idMenu]}
                    </span>
                </li>`;
        }

        // Insertar el módulo
        sidebar.innerHTML += `
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center" href="/${p.strNombreModulo.replace(/\s+/g, '-').toLowerCase()}">
                    <i class="bi bi-circle-fill me-2" style="font-size: 10px;"></i>
                    <span>${p.strNombreModulo}</span>
                </a>
            </li>`;
    });
}

    // 5. ACTUALIZAR BREADCRUMBS 
    function actualizarBreadcrumb(modulo) {
        const breadcrumb = document.getElementById('breadcrumbArea');
        breadcrumb.innerHTML = `
            <li class="breadcrumb-item"><a href="#">Seguridad</a></li>
            <li class="breadcrumb-item active">${modulo}</li>
        `;
    }

    function logout() {
        localStorage.clear();
        window.location.href = '<?= base_url("login") ?>';
    }
</script>
</body>
</html>