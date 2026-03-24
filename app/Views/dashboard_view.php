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
        body { background: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        #sidebar { min-width: 250px; max-width: 250px; background: #2c3e50; color: white; min-height: 100vh; transition: all 0.3s; }
        #sidebar .nav-link { color: rgba(255,255,255,0.7); border-radius: 5px; margin: 5px 10px; cursor: pointer; transition: 0.2s; }
        #sidebar .nav-link:hover, #sidebar .nav-link.active { background: #34495e; color: white; }
        .nav-small-cap { color: #8e9aaf; font-size: 0.75rem; letter-spacing: 1px; font-weight: bold; }
        .main-content { width: 100%; }
        .top-navbar { background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.08); }
        .user-img { object-fit: cover; border: 2px solid #3498db; }
    </style>
</head>
<body>

<div class="d-flex">
    <nav id="sidebar">
        <div class="p-4">
            <h4 class="text-center text-white m-0">Mi Empresa</h4>
        </div>
        <hr class="mx-3 opacity-25">
        
        <ul class="nav flex-column" id="sidebar-dinamico">
            </ul>

        <hr class="mx-3 opacity-25">
        <div class="px-3 pb-4">
            <button onclick="logout()" class="btn btn-outline-danger btn-sm w-100">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </button>
        </div>
    </nav>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg top-navbar px-4 py-2">
            <div class="container-fluid">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" id="breadcrumbArea">
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                    </ol>
                </nav>
                <div class="ms-auto d-flex align-items-center">
                    <span id="userNameDisplay" class="me-3 fw-semibold text-dark"></span>
                    <img id="userImgDisplay" src="" class="rounded-circle user-img" width="40" height="40" onerror="this.src='https://via.placeholder.com/40'">
                </div>
            </div>
        </nav>

        <div class="container-fluid p-4" id="mainWrapper">
            <div class="card p-5 text-center shadow-sm border-0">
                <div class="card-body">
                    <i class="bi bi-speedometer2 text-primary mb-3" style="font-size: 3rem;"></i>
                    <h2 class="fw-bold">¡Bienvenido al Sistema!</h2>
                    <p class="text-muted">Selecciona un módulo del menú lateral para gestionar la información.</p>
                </div>
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

    // 2. MOSTRAR DATOS DEL USUARIO (Protección contra null)
    if (user) {
        document.getElementById('userNameDisplay').innerText = user.nombre || 'Usuario';
        document.getElementById('userImgDisplay').src = user.foto || '';
    }

    // 3. INICIALIZAR CARGA DEL MENÚ
    document.addEventListener('DOMContentLoaded', () => {
        renderizarMenuCompleto();
    });

    // 4. FUNCIÓN MAESTRA: RENDERIZAR MENÚ DESDE LA BD
    async function renderizarMenuCompleto() {
        const sidebar = document.getElementById('sidebar-dinamico');

        try {
            // Fetch al controlador Menu.php con el idPerfil del usuario
            const response = await fetch(`<?= base_url('menu/obtenerMenu') ?>?idPerfil=${user.idPerfil}`, {
                headers: { 
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Error al obtener el menú');
            
            const datos = await response.json();
            sidebar.innerHTML = ''; 

            // Nombres de los menús según el ID en la tabla 'Menu'
            const nombresMenus = {
                1: 'SEGURIDAD',
                2: 'PRINCIPAL 1',
                3: 'PRINCIPAL 2'
            };

            let currentMenuId = null;

            datos.forEach(p => {
                // Si cambiamos de grupo (ej: de Seguridad a Principal 1), insertamos el encabezado
                if (p.idMenu !== currentMenuId) {
                    currentMenuId = p.idMenu;
                    sidebar.innerHTML += `
                        <li class="nav-small-cap mt-4 mb-2 px-3 text-uppercase">
                            ${nombresMenus[p.idMenu] || 'OTRO'}
                        </li>`;
                }

                // Insertar el enlace del módulo (se convierte el nombre a minúsculas y se quitan espacios para la URL)
                const urlModulo = p.strNombreModulo.replace(/\s+/g, '-').toLowerCase();
                
                sidebar.innerHTML += `
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" onclick="actualizarBreadcrumb('${p.strNombreModulo}')" href="<?= base_url() ?>${urlModulo}">
                            <i class="bi bi-chevron-right me-2" style="font-size: 10px;"></i>
                            <span>${p.strNombreModulo}</span>
                        </a>
                    </li>`;
            });

            if (datos.length === 0) {
                sidebar.innerHTML = '<li class="px-3 text-muted small">Sin módulos asignados</li>';
            }

        } catch (error) {
            console.error("Error crítico al cargar menú:", error);
            sidebar.innerHTML = '<li class="px-3 text-danger small">Error de conexión</li>';
        }
    }

    // 5. ACTUALIZAR BREADCRUMBS DINÁMICAMENTE
    function actualizarBreadcrumb(modulo) {
        const breadcrumb = document.getElementById('breadcrumbArea');
        breadcrumb.innerHTML = `
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Inicio</a></li>
            <li class="breadcrumb-item active text-capitalize">${modulo}</li>
        `;
    }

    // 6. CERRAR SESIÓN
    function logout() {
        localStorage.clear();
        window.location.href = '<?= base_url("login") ?>';
    }
</script>
</body>
</html>