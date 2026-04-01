<div class="container-fluid py-4 fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-layers text-primary me-2"></i> Gestión de Módulos</h4>
            <small class="text-muted" id="modulo-total-registros">Cargando...</small>
        </div>
        <button class="btn btn-primary btn-sm" onclick="appModulo.prepararNuevo()">+ Nuevo Módulo</button>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Módulo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-modulos" class="small">
                    <tr><td colspan="3" class="text-center">Cargando datos...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <nav class="d-flex justify-content-center">
                <ul class="pagination pagination-sm mb-0" id="paginacion-modulos"></ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="modalModulo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 id="modalTituloModulo">Nuevo Módulo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formModulo" onsubmit="appModulo.guardar(event)">
                <div class="modal-body">
                    <input type="hidden" id="modulo_id" name="id">
                    <div class="mb-3">
                        <label class="form-label" for="strNombreModulo">Nombre del Módulo</label>
                        <input type="text" id="strNombreModulo" name="strNombreModulo" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
window.appModulo = {
    paginaActual: 1,
    modalInstance: null,

    init: function() {
        const el = document.getElementById('modalModulo');
        if (el) this.modalInstance = new bootstrap.Modal(el);
        this.listar();
    },

    listar: async function() {
        try {
            const res = await fetch('<?= base_url('modulo/listar') ?>');
            const datos = await res.json();
            const tbody = document.getElementById('tbody-modulos');
            tbody.innerHTML = '';

            if (datos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No hay módulos.</td></tr>';
                document.getElementById('modulo-total-registros').textContent = '0 registros';
                return;
            }

            datos.forEach(item => {
                tbody.innerHTML += `<tr><td>${item.id}</td><td>${item.strNombreModulo}</td><td class="text-end"><button class="btn btn-sm btn-warning me-1" onclick="appModulo.editar(${item.id}, '${item.strNombreModulo}')">Editar</button><button class="btn btn-sm btn-danger" onclick="appModulo.eliminar(${item.id})">Eliminar</button></td></tr>`;
            });

            document.getElementById('modulo-total-registros').textContent = `${datos.length} registros`;
            document.getElementById('paginacion-modulos').innerHTML = '';
        } catch (err) {
            console.error('Error al listar módulos', err);
        }
    },

    prepararNuevo: function() {
        document.getElementById('modalTituloModulo').textContent = 'Nuevo Módulo';
        document.getElementById('formModulo').reset();
        document.getElementById('modulo_id').value = '';
        this.modalInstance.show();
    },

    editar: function(id, nombre) {
        document.getElementById('modalTituloModulo').textContent = 'Editar Módulo';
        document.getElementById('modulo_id').value = id;
        document.getElementById('strNombreModulo').value = nombre;
        this.modalInstance.show();
    },

    guardar: async function(e) {
        e.preventDefault();
        const id = document.getElementById('modulo_id').value;
        const form = new FormData(document.getElementById('formModulo'));
        const csrf = appModulo.getCsrfToken();
        if (csrf) form.append('csrf_test_name', csrf);

        if (id) form.append('id', id);

        const resp = await fetch('<?= base_url('modulo/guardar') ?>', {
            method: 'POST',
            body: form,
            headers: csrf ? { 'X-CSRF-TOKEN': csrf } : {}
        });
        const json = await resp.json();

        if (resp.ok) {
            this.modalInstance.hide();
            this.listar();
        } else {
            alert(json.message || json.errors?.[0] || 'Error al guardar');
        }
    },

    eliminar: async function(id) {
        if (!confirm('¿Eliminar este módulo?')) return;

        const csrf = appModulo.getCsrfToken();
        const resp = await fetch('<?= base_url('modulo') ?>/' + id, { 
            method: 'DELETE',
            headers: csrf ? { 'X-CSRF-TOKEN': csrf } : {}
        });
        if (resp.ok) this.listar();
        else alert('Error al eliminar');
    },

    getCsrfToken: function() {
        let token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (token) return token;
        const name = 'csrf_cookie_name=';
        const decodedCookie = decodeURIComponent(document.cookie);
        const cookieArray = decodedCookie.split(';');
        for (let cookie of cookieArray) {
            cookie = cookie.trim();
            if (cookie.indexOf(name) === 0) return cookie.substring(name.length);
        }
        return '';
    }
};

window.moduleInit = window.moduleInit || function() { if(window.appModulo) window.appModulo.init(); };
</script>