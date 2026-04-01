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

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Contenido</h5>
                                <div class="d-flex gap-2">
                                    <input id="txtBuscarPrincipal" type="search" class="form-control form-control-sm" style="max-width: 260px;" placeholder="Buscar..." maxlength="100" />
                                    <button id="btnNuevoPrincipal" class="btn btn-sm btn-primary" <?= $permisos['bitAgregar'] ? '' : 'disabled' ?>>
                                        <i class="bi bi-plus-circle"></i> Nuevo
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover" id="tablaPrincipal">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Detalle</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const modulo = '<?= strtolower(str_replace(' ', '', $titulo)) ?>';
    const permisos = <?= json_encode($permisos) ?>;

    const datosDefecto = {
        'principal1.1': [
            { nombre: 'Ana García', detalle: 'Ventas - Gerente Regional' },
            { nombre: 'Carlos López', detalle: 'TI - Desarrollador Backend' },
            { nombre: 'María Rodríguez', detalle: 'RH - Reclutadora' }
        ],
        'principal1.2': [
            { nombre: 'FAC-10001', detalle: 'Corp Industrial $154,200.00 (Vigente)' },
            { nombre: 'FAC-10002', detalle: 'Serv Logísticos $3,250.00 (Cancelado)' }
        ],
        'principal2.1': [
            { nombre: 'Reporte-01', detalle: 'Análisis mensual de ventas' }
        ],
        'principal2.2': [
            { nombre: 'Reporte-02', detalle: 'Existencias actuales' }
        ]
    };

    const filas = datosDefecto[modulo] || [];
    const tbody = document.querySelector('#tablaPrincipal tbody');

    const renderFilas = () => {
        const filtro = document.querySelector('#txtBuscarPrincipal').value.toLowerCase();
        const filtrado = filas.filter(r => r.nombre.toLowerCase().includes(filtro) || r.detalle.toLowerCase().includes(filtro));

        tbody.innerHTML = '';

        if (!permisos.bitConsulta) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">No tienes permiso de consulta.</td></tr>';
            return;
        }

        if (filtrado.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Sin registros.</td></tr>';
            return;
        }

        filtrado.forEach(item => {
            const acciones = [];
            if (permisos.bitEditar) acciones.push('<button class="btn btn-sm btn-outline-warning me-1">Editar</button>');
            if (permisos.bitEliminar) acciones.push('<button class="btn btn-sm btn-outline-danger">Eliminar</button>');

            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td>${item.nombre}</td>
                    <td>${item.detalle}</td>
                    <td class="text-end">${acciones.join(' ')}</td>
                </tr>
            `);
        });
    };

    document.querySelector('#txtBuscarPrincipal').addEventListener('input', renderFilas);
    document.querySelector('#btnNuevoPrincipal').addEventListener('click', () => {
        if (!permisos.bitAgregar) return;
        alert('Crear nuevo registro [funcionalidad de muestra]');
    });

    renderFilas();
})();
</script>

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