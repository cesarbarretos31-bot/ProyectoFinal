<?php 
    $session = session(); 
    // Si por alguna razón llega aquí sin sesión (aunque el filtro debería pararlo), 
    // estos valores serán nulos y el script no tronará.
    $nombre   = $session->get('nombre') ?? 'Usuario';
    $foto     = $session->get('foto')   ?? '';
    $idPerfil = $session->get('idPerfil');
?>
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
        .nav-small-cap { color: #8e9aaf; font-size: 0.75rem; letter-spacing: 1px; font-weight: bold; margin-top: 20px; }
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
        <div class="px-3 pb-4 mt-auto">
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm w-100">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
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
                    <span class="me-3 fw-semibold text-dark"><?= $nombre ?></span>
                    <img src="<?= $foto ?>" class="rounded-circle user-img" width="40" height="40" onerror="this.src='https://via.placeholder.com/40'">
                </div>
            </div>
        </nav>

        <div class="container-fluid p-4" id="mainWrapper">
            <div class="card p-5 text-center shadow-sm border-0">
                <div class="card-body">
                    <i class="bi bi-speedometer2 text-primary mb-3" style="font-size: 3rem;"></i>
                    <h2 class="fw-bold">¡Bienvenido, <?= explode(' ', $nombre)[0] ?>!</h2>
                    <p class="text-muted">Selecciona un módulo del menú lateral para gestionar la información.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Variables obtenidas directamente de PHP (Sesión del servidor)
    const PERFIL_ID = "<?= session()->get('idPerfil') ?>";

    document.addEventListener('DOMContentLoaded', () => {
        if (PERFIL_ID) {
            renderizarMenuCompleto();
        } else {
            // Si no hay perfil en sesión, algo salió mal, redirigir al login
            window.location.href = '<?= base_url("login") ?>';
        }
    });

    async function renderizarMenuCompleto() {
    const sidebar = document.getElementById('sidebar-dinamico');
    try {
        const response = await fetch(`<?= base_url('menu/obtenerMenu') ?>?idPerfil=${PERFIL_ID}`);
        const datos = await response.json();
        sidebar.innerHTML = ''; 

        let currentMenuId = null;
        datos.forEach(p => {
            // ... (tu lógica de títulos de menú igual) ...

            // CAMBIO AQUÍ: Usamos onclick en lugar de un href real
            sidebar.innerHTML += `
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center" href="javascript:void(0)" 
                       onclick="cargarModulo('${p.strNombreModulo}')">
                        <i class="bi bi-circle-fill me-2" style="font-size: 8px;"></i>
                        <span>${p.strNombreModulo}</span>
                    </a>
                </li>`;
        });
    } catch (error) { console.error("Error:", error); }
}

async function cargarModulo(nombre) {
    const contenedor = document.getElementById('mainWrapper');
    const urlSlug = nombre.toLowerCase().replace(/\s+/g, '-'); 
    
    try {
        // Usamos la URL absoluta directa a tu servidor Railway
        const res = await fetch(`https://proyectofinal-production-e9e1.up.railway.app/index.php/${urlSlug}/vista`);
        const html = await res.text();
        
        // 1. Inyectamos la vista
        contenedor.innerHTML = html;
        
        // 2. Extraemos y forzamos la ejecución de los scripts de forma limpia
        const scripts = contenedor.getElementsByTagName('script');
        for (let i = 0; i < scripts.length; i++) {
            try {
                // textContent ignora etiquetas HTML fantasma
                window.eval(scripts[i].textContent);
            } catch (errScript) {
                console.error("Error aislando el script:", errScript);
            }
        }
    } catch (e) {
        contenedor.innerHTML = '<div class="alert alert-danger">Fallo al traer la vista del módulo</div>';
    }
}
</script>
</body>
</html>