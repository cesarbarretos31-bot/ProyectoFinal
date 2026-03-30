<?php 
    $session = session(); 
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .wrapper-container { display: flex; min-height: 100vh; }
        #sidebar { min-width: 250px; max-width: 250px; background: #2c3e50; color: white; transition: all 0.3s; display: flex; flex-direction: column; }
        #sidebar .nav-link { color: rgba(255,255,255,0.7); border-radius: 5px; margin: 2px 10px; cursor: pointer; transition: 0.2s; padding: 10px 15px; }
        #sidebar .nav-link:hover, #sidebar .nav-link.active { background: #34495e; color: white; }
        .nav-small-cap { color: #8e9aaf; font-size: 0.75rem; letter-spacing: 1px; font-weight: bold; margin-top: 20px; padding-left: 15px; }
        .main-content { flex-grow: 1; display: flex; flex-direction: column; width: calc(100% - 250px); }
        .top-navbar { background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.08); z-index: 10; }
        .user-img { object-fit: cover; border: 2px solid #3498db; }
        #mainWrapper { flex-grow: 1; overflow-y: auto; }
    </style>
</head>
<body>

<div class="wrapper-container">
    <nav id="sidebar" class="shadow-lg">
        <div class="p-4">
            <h4 class="text-center text-white fw-bold m-0"><i class="bi bi-buildings"></i> Mi Empresa</h4>
        </div>
        <hr class="mx-3 opacity-25 mt-0">
        
        <div class="flex-grow-1 overflow-y-auto">
            <ul class="nav flex-column" id="sidebar-dinamico">
                </ul>
        </div>

        <hr class="mx-3 opacity-25 mb-0">
        <div class="p-3">
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm w-100">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </nav>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg top-navbar px-4 py-3">
            <div class="container-fluid px-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" id="breadcrumbArea">
                        <li class="breadcrumb-item"><a href="javascript:void(0)" onclick="location.reload()" class="text-decoration-none">Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3 fw-semibold text-dark"><?= $nombre ?></span>
                    <img src="<?= $foto ?>" class="rounded-circle user-img shadow-sm" width="40" height="40" 
                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($nombre) ?>&background=random';">
                </div>
            </div>
        </nav>

        <div class="container-fluid p-4" id="mainWrapper">
            <div class="card p-5 text-center shadow-sm border-0 mt-4 fade-in">
                <div class="card-body py-5">
                    <i class="bi bi-speedometer2 text-primary mb-3" style="font-size: 4rem;"></i>
                 <h2 class="fw-bold text-dark">¡Bienvenido, <?= explode(' ', $nombre) ?>!</h2>
                    <p class="text-muted lead">Selecciona un módulo del menú lateral para comenzar a gestionar la información.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const PERFIL_ID = "<?= session()->get('idPerfil') ?>";

    document.addEventListener('DOMContentLoaded', () => {
        if (PERFIL_ID) {
            renderizarMenuCompleto();
        } else {
            window.location.href = '<?= base_url("login") ?>';
        }
    });

    async function renderizarMenuCompleto() {
        const sidebar = document.getElementById('sidebar-dinamico');
        try {
            const response = await fetch(`<?= base_url('menu/obtenerMenu') ?>?idPerfil=${PERFIL_ID}`);
            const datos = await response.json();
            sidebar.innerHTML = ''; 

            datos.forEach(p => {
                // Aquí mantengo tu lógica original, inyectando los items.
                sidebar.innerHTML += `
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center module-link" href="javascript:void(0)" 
                           onclick="cargarModulo('${p.strNombreModulo}', this)">
                            <i class="bi bi-circle-fill me-2" style="font-size: 8px;"></i>
                            <span>${p.strNombreModulo}</span>
                        </a>
                    </li>`;
            });
        } catch (error) { console.error("Error al cargar menú:", error); }
    }

    async function cargarModulo(nombre, elementoMenu) {
        const contenedor = document.getElementById('mainWrapper');
        const breadcrumb = document.getElementById('breadcrumbArea');
        const slug = nombre.toLowerCase().replace(/\s+/g, '-'); 
        
        // 1. Actualizar estilos del menú activo
        document.querySelectorAll('.module-link').forEach(el => el.classList.remove('active'));
        if(elementoMenu) elementoMenu.classList.add('active');

        // 2. Actualizar Breadcrumbs (Requisito del PDF)
        breadcrumb.innerHTML = `
            <li class="breadcrumb-item"><a href="javascript:void(0)" onclick="location.reload()" class="text-decoration-none">Inicio</a></li>
            <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">${nombre}</li>
        `;

        // 3. Mostrar indicador de carga
        contenedor.innerHTML = `<div class="text-center mt-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Cargando módulo ${nombre}...</p></div>`;
        
        try {
            // FIX: Uso de base_url() en lugar de la URL quemada de Railway
            const urlFetch = `<?= base_url('index.php/') ?>${slug}/vista`;
            const res = await fetch(urlFetch);
            
            if(!res.ok) throw new Error("Error HTTP " + res.status);
            
            const html = await res.text();
            contenedor.innerHTML = html;
            
            // 4. Ejecutar scripts de forma segura
            const scripts = contenedor.querySelectorAll('script');
            scripts.forEach(oldScript => {
                const newScript = document.createElement('script');
                newScript.text = oldScript.innerText;
                // Agregamos un try-catch interno por si el script del módulo tiene errores
                try {
                    document.body.appendChild(newScript);
                } catch (e) {
                    console.error(`Error al ejecutar script del módulo ${nombre}:`, e);
                } finally {
                    document.body.removeChild(newScript);
                }
            });
        } catch (e) {
            console.error("Error cargando vista:", e);
            contenedor.innerHTML = `
                <div class="alert alert-danger shadow-sm mt-4">
                    <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Error de conexión</h5>
                    <p>No se pudo cargar el módulo <b>${nombre}</b>.</p>
                    <hr>
                    <small>Verifica que el controlador <code>${nombre}.php</code> y su método <code>vista()</code> existan.</small>
                </div>`;
        }
    }
</script>
</body>
</html>