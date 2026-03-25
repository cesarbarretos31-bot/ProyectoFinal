<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        #sidebar { min-width: 250px; max-width: 250px; background: #2c3e50; color: white; min-height: 100vh; }
        #sidebar .nav-link { color: rgba(255,255,255,0.7); margin: 5px 10px; cursor: pointer; text-decoration: none; display: block; padding: 10px; }
        #sidebar .nav-link:hover { background: #34495e; color: white; border-radius: 5px; }
        .main-content { width: 100%; }
        .top-navbar { background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.08); }
        .user-img { object-fit: cover; border: 2px solid #3498db; }
    </style>
</head>
<body><div class="d-flex">
    <nav id="sidebar">
        <div class="p-4"><h4 class="text-center text-white m-0">Mi Empresa</h4></div>
        <hr class="mx-3 opacity-25">
        <ul class="nav flex-column" id="sidebar-dinamico">
            </ul>
        <div class="px-3 pb-4 mt-auto">
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm w-100">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg top-navbar px-4 py-2">
            <div class="container-fluid">
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3 fw-semibold text-dark"><?= session()->get('nombre') ?></span>
                    <img src="<?= session()->get('foto') ?>" class="rounded-circle user-img" width="40" height="40" 
                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= session()->get('nombre') ?>';">
                </div>
            </div>
        </nav>
        <div class="container-fluid p-4" id="mainWrapper">
            <div class="card p-5 text-center shadow-sm border-0">
                <h2>¡Bienvenido!</h2>
                <p>Selecciona un módulo a la izquierda.</p>
            </div>
        </div>
    </div>
</div>