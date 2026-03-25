<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Seguridad</a></li>
        <li class="breadcrumb-item active" aria-current="page">Perfil</li>
    </ol>
</nav>

<div class="card shadow-sm border-0 animate__animated animate__fadeIn">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-person-badge me-2"></i>Gestión de Perfiles</h5>
        <button class="btn btn-primary btn-sm" onclick="abrirModalNuevo()">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Perfil
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Nombre Perfil</th>
                        <th>Administrador</th>
                        <th class="text-center" style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaPerfiles">
                    <tr><td colspan="4" class="text-center p-4">Iniciando carga...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="paginacionContainer" class="d-flex justify-content-center mt-3"></div>
    </div>
</div>

<div class="modal fade" id="modalPerfil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Registrar Perfil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPerfil">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre del Perfil</label>
                        <input type="text" name="strNombrePerfil" class="form-control" placeholder="Ej: Supervisor" required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="bitAdministrador" value="1" id="checkAdmin">
                        <label class="form-check-label small" for="checkAdmin">¿Es Administrador?</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary btn-sm">Guardar Datos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    let paginaActual = 1;
    const modalEl = document.getElementById('modalPerfil');
    const bsModal = new bootstrap.Modal(modalEl);

    // Función para cargar los datos
    async function cargarPerfiles(pagina = 1) {
        paginaActual = pagina;
        const tabla = document.getElementById('tablaPerfiles');
        const paginador = document.getElementById('paginacionContainer');

        try {
            // Usamos la ruta que confirmó tu JSON
            const response = await fetch(`<?= base_url('index.php/perfil') ?>?page=${pagina}`);
            const res = await response.json();

            let html = '';
            if (res.perfiles && res.perfiles.length > 0) {
                res.perfiles.forEach(p => {
                    html += `
                        <tr>
                            <td>${p.id}</td>
                            <td>${p.strNombrePerfil}</td>
                            <td>
                                <span class="badge ${p.bitAdministrador == '1' ? 'bg-success' : 'bg-secondary'}">
                                    ${p.bitAdministrador == '1' ? 'SÍ' : 'NO'}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm" onclick="borrarPerfil(${p.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>`;
                });
            } else {
                html = '<tr><td colspan="4" class="text-center">No hay datos disponibles.</td></tr>';
            }
            
            tabla.innerHTML = html;
            if (paginador) paginador.innerHTML = res.pager || '';

        } catch (err) {
            console.error("Error cargando tabla:", err);
            tabla.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error de conexión con el servidor.</td></tr>';
        }
    }

    // Funciones globales vinculadas al módulo
    window.abrirModalNuevo = () => {
        document.getElementById('formPerfil').reset();
        bsModal.show();
    };

    window.borrarPerfil = async (id) => {
        if (!confirm("¿Eliminar este perfil?")) return;
        try {
            const response = await fetch(`<?= base_url("index.php/perfil/eliminar") ?>/${id}`, { method: 'DELETE' });
            if (response.ok) cargarPerfiles(paginaActual);
        } catch (e) { alert("Error al intentar eliminar"); }
    };

    document.getElementById('formPerfil').onsubmit = async (e) => {
        e.preventDefault();
        try {
            const response = await fetch('<?= base_url("index.php/perfil/crear") ?>', {
                method: 'POST',
                body: new FormData(e.target)
            });
            if (response.ok) {
                bsModal.hide();
                cargarPerfiles(paginaActual);
            }
        } catch (e) { alert("Error al guardar los datos"); }
    };

    // Primera carga al abrir el módulo
    cargarPerfiles();
})();
</script>