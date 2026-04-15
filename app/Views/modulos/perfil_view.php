<div class="container-fluid py-4 fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-shield-lock text-primary me-2"></i> Gestión de Perfiles</h4>
            <small class="text-muted" id="total-registros">Cargando...</small>
        </div>
        <?php if ($permisos['bitAgregar']): ?>
        <button class="btn btn-primary btn-sm shadow-sm" onclick="appPerfil.prepararNuevo()">
            <i class="bi bi-plus-lg"></i> + Nuevo Perfil
        </button>
        <?php endif; ?>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0" id="txtBuscarPerfil" 
                       placeholder="Buscar por nombre... (búsqueda automática)" maxlength="100" onkeyup="appPerfil.buscarTiempoReal()">
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Perfil</th>
                        <th>Administrador</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-perfiles" class="small">
                    <tr><td colspan="4" class="text-center">Cargando datos...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <nav class="d-flex justify-content-center">
                <ul class="pagination pagination-sm mb-0" id="paginacion-perfiles"></ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPerfil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTituloPerfil">Nuevo Perfil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPerfil" onsubmit="appPerfil.guardar(event)">
                <div class="modal-body">
                    <input type="hidden" name="id" id="perfil_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="strNombrePerfil">Nombre del Perfil</label>
                        <input type="text" name="strNombrePerfil" id="strNombrePerfil" class="form-control" maxlength="100" pattern="[a-zA-Z0-9\s]+" title="Solo letras, números y espacios. Máximo 100 caracteres." required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="bitAdministrador" id="bitAdministrador" value="1">
                        <label class="form-check-label" for="bitAdministrador">Privilegios de Administrador</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Datos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Encapsulamos la lógica en un objeto global para evitar colisiones al recargar la vista
window.appPerfil = {
    paginaActual: 1,
    timeoutBusqueda: null,
    modalInstance: null,

    init: function() {
        // Inicializar el modal de Bootstrap solo cuando se carga la vista
        const modalEl = document.getElementById('modalPerfil');
        if(modalEl) {
            this.modalInstance = new bootstrap.Modal(modalEl);
        }
        this.listar();
    },

    buscarTiempoReal: function() {
        clearTimeout(this.timeoutBusqueda);
        this.timeoutBusqueda = setTimeout(() => {
            this.paginaActual = 1; 
            this.listar();
        }, 400); 
    },

    listar: async function() {
        const busqueda = document.getElementById('txtBuscarPerfil').value || '';
        const urlListar = `<?= base_url('perfil/listar') ?>?page=${this.paginaActual}&search=${busqueda}`;
        
        try {
            const resp = await fetch(urlListar);
            if (!resp.ok) throw new Error("Error en la respuesta del servidor");
            
            const res = await resp.json();
            const tbody = document.getElementById('tbody-perfiles');
            tbody.innerHTML = '';

            if(res.data && res.data.length > 0) {
                res.data.forEach(p => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${p.id}</td>
                            <td class="fw-bold">${p.strNombrePerfil}</td>
                            <td>${p.bitAdministrador == 1 ? '<span class="badge bg-success-subtle text-success border">Sí</span>' : '<span class="badge bg-light text-muted border">No</span>'}</td>
                            <td class="text-end">
                                <?php if ($permisos['bitEditar']): ?>
                                <button class="btn btn-link text-warning p-1" onclick="appPerfil.prepararEditar(${p.id}, '${p.strNombrePerfil}', ${p.bitAdministrador})"><i class="bi bi-pencil"></i></button>
                                <?php endif; ?>
                                <?php if ($permisos['bitEliminar']): ?>
                                <button class="btn btn-link text-danger p-1" onclick="appPerfil.eliminar(${p.id})"><i class="bi bi-trash"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">No se encontraron registros.</td></tr>`;
            }

            this.actualizarPaginacion(res.pager);
            document.getElementById('total-registros').innerText = `${res.pager.totalRows} registros encontrados`;
        } catch (err) { 
            console.error("Error al listar perfiles:", err);
            document.getElementById('tbody-perfiles').innerHTML = `<tr><td colspan="4" class="text-center text-danger">Error al cargar los datos. Revisa la consola.</td></tr>`;
        }
    },

    guardar: async function(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const csrf = appPerfil.getCsrfToken();
        if (csrf) formData.append('csrf_test_name', csrf);

        const id = document.getElementById('perfil_id').value;
        if (id) formData.append('id', id);

        // Asegurar que bitAdministrador siempre sea 0 o 1
        const isAdmin = document.getElementById('bitAdministrador').checked ? 1 : 0;
        formData.set('bitAdministrador', isAdmin);

        try {
            const resp = await fetch('<?= base_url("perfil/guardar") ?>', {
                method: 'POST',
                headers: csrf ? { 'X-CSRF-TOKEN': csrf } : {},
                body: formData
            });
            const res = await resp.json();

            if (resp.ok) {
                this.modalInstance.hide();
                this.listar();
            } else {
                alert("Error al guardar: " + (res.message || res.errors?.[0] || "Problema en el servidor"));
            }
        } catch (err) {
            console.error("Error al guardar:", err);
            alert("Error: " + err.message);
        }
    },

    eliminar: async function(id) {
        if(!confirm('¿Seguro que desea eliminar este perfil?')) return;
        
        try {
            const csrf = appPerfil.getCsrfToken();
            const formData = new FormData();
            if (csrf) formData.append('csrf_test_name', csrf);
            
            const resp = await fetch(`<?= base_url('perfil/eliminar') ?>/${id}`, { 
                method: 'POST',
                headers: csrf ? { 'X-CSRF-TOKEN': csrf } : {},
                body: formData
            });
            const res = await resp.json();
            if(resp.ok) this.listar();
            else alert("Error: " + (res.message || "No se pudo eliminar"));
        } catch (err) {
            console.error("Error al eliminar:", err);
            alert("Error: " + err.message);
        }
    },

    getCsrfToken: function() {
        // Intentar obtener del meta tag primero
        let token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (token) return token;
        
        // Si no, intentar del cookie
        const name = 'csrf_cookie_name=';
        const decodedCookie = decodeURIComponent(document.cookie);
        const cookieArray = decodedCookie.split(';');
        for (let cookie of cookieArray) {
            cookie = cookie.trim();
            if (cookie.indexOf(name) === 0) {
                return cookie.substring(name.length, cookie.length);
            }
        }
        return '';
    },

    prepararNuevo: function() {
        document.getElementById('modalTituloPerfil').innerText = "Nuevo Perfil";
        document.getElementById('formPerfil').reset();
        document.getElementById('perfil_id').value = "";
        this.modalInstance.show();
    },

    prepararEditar: function(id, nombre, admin) {
        document.getElementById('modalTituloPerfil').innerText = "Editar Perfil";
        document.getElementById('perfil_id').value = id;
        document.getElementById('strNombrePerfil').value = nombre;
        document.getElementById('bitAdministrador').checked = (admin == 1);
        this.modalInstance.show();
    },

    actualizarPaginacion: function(pager) {
        const cont = document.getElementById('paginacion-perfiles');
        cont.innerHTML = '';
        if(!pager || pager.total <= 1) return; // No mostrar si solo hay 1 página
        
        for (let i = 1; i <= pager.total; i++) {
            cont.innerHTML += `
                <li class="page-item ${i === pager.current ? 'active' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="appPerfil.paginaActual=${i}; appPerfil.listar();">${i}</a>
                </li>
            `;
        }
    }
};

// Arrancar la aplicación de Perfil
window.appPerfil.init();
</script>

<script>
// Permisos del usuario actual para este módulo
window.permisosPerfil = {
    bitConsulta: <?= $permisos['bitConsulta'] ?>,
    bitAgregar: <?= $permisos['bitAgregar'] ?>,
    bitEditar: <?= $permisos['bitEditar'] ?>,
    bitEliminar: <?= $permisos['bitEliminar'] ?>,
    bitDetalle: <?= $permisos['bitDetalle'] ?>
};
</script>