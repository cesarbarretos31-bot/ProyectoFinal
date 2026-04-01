<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="javascript:void(0)" onclick="location.reload()" class="text-decoration-none">Inicio</a></li>
    <li class="breadcrumb-item active" id="breadcrumbModulo"><?= $titulo ?></li>
  </ol>
</nav>

<div class="container-fluid py-4 fade-in">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Módulo: <?= $titulo ?></h5>
                        <small class="text-white-50">Sistema Corporativo</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Panel de Información -->
                        <div class="col-lg-8">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="bi bi-info-circle-fill me-2"></i>Información del Módulo
                                    </h6>
                                    <p class="card-text text-muted mb-3">
                                        Este módulo está diseñado para gestionar información específica del sistema.
                                        Aquí puedes realizar operaciones relacionadas con <?= strtolower($titulo) ?>.
                                    </p>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                <span class="small">Módulo activo</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-shield-check text-primary me-2"></i>
                                                <span class="small">Acceso autorizado</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Panel de Acciones Rápidas -->
                        <div class="col-lg-4">
                            <div class="card border-0">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 text-primary">
                                        <i class="bi bi-lightning-charge-fill me-2"></i>Acciones Rápidas
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary btn-sm" disabled>
                                            <i class="bi bi-plus-circle me-2"></i>Crear Nuevo
                                        </button>
                                        <button class="btn btn-outline-success btn-sm" disabled>
                                            <i class="bi bi-search me-2"></i>Buscar Registros
                                        </button>
                                        <button class="btn btn-outline-warning btn-sm" disabled>
                                            <i class="bi bi-pencil me-2"></i>Editar Información
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" disabled>
                                            <i class="bi bi-trash me-2"></i>Eliminar Registros
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" disabled>
                                            <i class="bi bi-eye me-2"></i>Ver Detalles
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel de Estadísticas -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-0 bg-gradient-primary text-white">
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="d-flex flex-column">
                                                <h4 class="mb-0">0</h4>
                                                <small class="text-white-75">Registros Totales</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="d-flex flex-column">
                                                <h4 class="mb-0">0</h4>
                                                <small class="text-white-75">Activos</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="d-flex flex-column">
                                                <h4 class="mb-0">0</h4>
                                                <small class="text-white-75">Inactivos</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="d-flex flex-column">
                                                <h4 class="mb-0">0%</h4>
                                                <small class="text-white-75">Completitud</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel de Estado del Sistema -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-0">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="bi bi-check-circle-fill me-2"></i>Estado del Sistema
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-success border-0 mb-0" role="alert">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        <strong>Módulo operativo:</strong> El sistema está funcionando correctamente.
                                        Todas las funciones están disponibles.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.fade-in {
    animation: fadeIn 0.5s ease-in;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>