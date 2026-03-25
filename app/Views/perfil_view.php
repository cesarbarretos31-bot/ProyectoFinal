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
                    <tr><td colspan="4" class="text-center p-4">Cargando datos...</td></tr>
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
// Encapsulamos el módulo para evitar conflictos con otros componentes
(function() {
    let paginaActual = 1;
    const modalEl = document.getElementById('modalPerfil');
    const bsModal = new bootstrap.Modal(modalEl);

    // Hacer la función de abrir modal accesible globalmente para el botón
    window.abrirModalNuevo = () => {
        document.getElementById('formPerfil').reset();
        bsModal.show();
    };

    async function cargarPerfiles(pagina = 1) {
    paginaActual = pagina;
    const tabla = document.getElementById('tablaPerfiles');
    const paginador = document.getElementById('paginacionContainer');

    try {
        const response = await fetch(`<?= base_url('perfil') ?>?page=${pagina}`);
        
        // Obtenemos el texto plano primero para limpiarlo si es necesario
        let textoRaw = await response.text();
        
        // Si el texto trae comentarios de Debug, los podamos
        if (textoRaw.includes('/g, ""); 
            paginador.innerHTML = pagerHtml;
            // ... (resto de tu lógica de estilos de paginación)
        }
    } catch (err) {
        console.error("Error detallado:", err);
        tabla.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error al procesar datos.</td></tr>';
    }
}

    // Lógica para Guardar (POST)
    document.getElementById('formPerfil').onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        
        try {
            const response = await fetch('<?= base_url("perfil/crear") ?>', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                bsModal.hide();
                cargarPerfiles(paginaActual); // Recargar la tabla
            } else {
                alert("Error al guardar el perfil.");
            }
        } catch (err) {
            alert("Error de red.");
        }
    };

    // Lógica para Eliminar (DELETE)
    window.borrarPerfil = async (id) => {
        if (!confirm("¿Estás seguro de eliminar este perfil?")) return;
        
        try {
            const response = await fetch(`<?= base_url("perfil/eliminar") ?>/${id}`, {
                method: 'DELETE'
            });

            if (response.ok) {
                cargarPerfiles(paginaActual);
            } else {
                alert("No se pudo eliminar. Es posible que el perfil esté asignado a un usuario activo.");
            }
        } catch (err) {
            alert("Error al procesar la solicitud.");
        }
    };

    // Iniciar el módulo
    cargarPerfiles();
})();
</script>