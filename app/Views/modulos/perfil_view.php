<div class="container-fluid py-4 fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-shield-lock text-primary me-2"></i> Gestión de Perfiles</h4>
            <small class="text-muted" id="total-registros">Cargando...</small>
        </div>
        <button class="btn btn-primary btn-sm shadow-sm" onclick="appPerfil.prepararNuevo()">
            <i class="bi bi-plus-lg"></i> + Nuevo Perfil
        </button>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0" id="txtBuscarPerfil" 
                       placeholder="Buscar por nombre... (búsqueda automática)" onkeyup="appPerfil.buscarTiempoReal()">
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
                        <input type="text" name="strNombrePerfil" id="strNombrePerfil" class="form-control" required>
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
                                <button class="btn btn-link text-warning p-1" onclick="appPerfil.prepararEditar(${p.id}, '${p.strNombrePerfil}', ${p.bitAdministrador})"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-link text-danger p-1" onclick="appPerfil.eliminar(${p.id})"><i class="bi bi-trash"></i></button>
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
        
        try {
            const resp = await fetch('<?= base_url("perfil/guardar") ?>', {
                method: 'POST',
                body: formData
            });
            const res = await resp.json();
            
            if(res.status === 'success') {
                this.modalInstance.hide();
                this.listar();
            } else {
                alert("Error al guardar: " + (res.message || "Problema en el servidor"));
            }
        } catch (err) {
            console.error("Error al guardar:", err);
        }
    },

    eliminar: async function(id) {
        if(!confirm('¿Seguro que desea eliminar este perfil?')) return;
        
        try {
            const resp = await fetch(`<?= base_url('perfil/eliminar') ?>/${id}`, { method: 'DELETE' });
            const res = await resp.json();
            if(res.status === 'success') this.listar();
        } catch (err) {
            console.error("Error al eliminar:", err);
        }
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