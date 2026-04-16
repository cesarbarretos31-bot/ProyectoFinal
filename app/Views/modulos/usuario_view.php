<div class="container-fluid py-4 fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <h4 class="fw-bold mb-0"><i class="bi bi-people text-primary me-2"></i> Gestión de Usuarios</h4>
            <input id="txtBuscarUsuario" type="search" class="form-control form-control-sm" style="width: 250px;" placeholder="Buscar usuario..." maxlength="100" onkeyup="appUsuario.buscar()">
        </div>
        <?php if ($permisos['bitAgregar']): ?>
        <button class="btn btn-primary btn-sm" onclick="appUsuario.prepararNuevo()">+ Nuevo Usuario</button>
        <?php endif; ?>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th>Usuario</th>
                        <th>Perfil</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-usuarios" class="small">
                    <tr><td colspan="5" class="text-center">Cargando datos...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <nav class="d-flex justify-content-center">
                <ul class="pagination pagination-sm mb-0" id="paginacion-usuarios"></ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 id="modalTituloUsuario">Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUsuario" onsubmit="appUsuario.guardar(event)">
                <div class="modal-body">
                    <input type="hidden" id="usuario_id" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="strNombreUsuario" id="strNombreUsuario" class="form-control" maxlength="100" pattern="[a-zA-Z0-9\s]+" title="Solo letras, números y espacios. Máximo 100 caracteres." required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Perfil</label>
                            <select name="idPerfil" id="idPerfil" class="form-select" required></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo</label>
                            <input type="email" name="strCorreo" id="strCorreo" class="form-control" maxlength="150" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="strNumeroCelular" id="strNumeroCelular" class="form-control" maxlength="15" pattern="[0-9]+" title="Solo números. Máximo 15 caracteres." required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="strPwd" id="strPwd" class="form-control" maxlength="80" minlength="6" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <select name="idEstado" class="form-select">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen</label>
                        <input type="file" name="strImagen" class="form-control">
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
window.appUsuario = {
    modalInstance: null,
    paginaActual: 1,
    filtro: '',

    init: async function() {
        const el = document.getElementById('modalUsuario');
        if (el) this.modalInstance = new bootstrap.Modal(el);
        await this.cargarPerfiles();
        this.listar();
    },

    cargarPerfiles: async function() {
        const respuesta = await fetch('<?= base_url('perfil/listar') ?>');
        const data = await respuesta.json();
        const select = document.getElementById('idPerfil');
        select.innerHTML = '<option value="">Seleccione</option>';
        data.data.forEach(p => {
            select.innerHTML += `<option value="${p.id}">${p.strNombrePerfil}</option>`;
        });
    },

    listar: async function() {
        const query = new URLSearchParams({ page: this.paginaActual || 1, search: this.filtro || '' });
        const resp = await fetch(`<?= base_url('usuario/listar') ?>?${query}`);
        const res = await resp.json();
        const usuarios = res.data || [];
        const tbody = document.getElementById('tbody-usuarios');
        tbody.innerHTML = '';

        if (usuarios.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No hay usuarios.</td></tr>';
            document.getElementById('paginacion-usuarios').innerHTML = '';
            return;
        }

        usuarios.forEach(u => {
            tbody.innerHTML += `<tr><td>${u.strNombreUsuario}</td><td>${u.perfil||'--'}</td><td>${u.idEstado==1 ? 'Activo' : 'Inactivo'}</td><td class="text-end">
            <?php if ($permisos['bitEditar']): ?>
            <button class="btn btn-sm btn-warning me-1" onclick="appUsuario.editar(${u.id})">Editar</button>
            <?php endif; ?>
            <?php if ($permisos['bitEliminar']): ?>
            <button class="btn btn-sm btn-danger" onclick="appUsuario.eliminar(${u.id})">Eliminar</button>
            <?php endif; ?>
            </td></tr>`;
        });

        this.paginacionUsuarios(res.pager);
    },

    prepararNuevo: function() {
        document.getElementById('modalTituloUsuario').textContent = 'Nuevo Usuario';
        document.getElementById('formUsuario').reset();
        document.getElementById('usuario_id').value = '';
        this.modalInstance.show();
    },

    editar: async function(id) {
        const resp = await fetch('<?= base_url('usuario/obtener') ?>/' + id);
        const usuario = await resp.json();
        document.getElementById('modalTituloUsuario').textContent = 'Editar Usuario';
        document.getElementById('usuario_id').value = usuario.id;
        document.getElementById('strNombreUsuario').value = usuario.strNombreUsuario;
        document.getElementById('idPerfil').value = usuario.idPerfil;
        document.getElementById('strCorreo').value = usuario.strCorreo;
        document.getElementById('strNumeroCelular').value = usuario.strNumeroCelular;
        document.getElementById('strPwd').value = '';
        document.querySelector('select[name="idEstado"]').value = usuario.idEstado;
        this.modalInstance.show();
    },

    guardar: async function(e) {
        e.preventDefault();
        const id = document.getElementById('usuario_id').value;
        const formData = new FormData(document.getElementById('formUsuario'));
        const csrf = appUsuario.getCsrfToken();
        if (csrf) formData.append('csrf_test_name', csrf);

        if (id) formData.append('id', id);

        const resp = await fetch('<?= base_url('usuario/guardar') ?>', {
            method: 'POST',
            body: formData,
            headers: csrf ? { 'X-CSRF-TOKEN': csrf } : {}
        });
        const result = await resp.json();

        if (resp.ok) {
            this.modalInstance.hide();
            this.listar();
        } else {
            alert(result.message || result.errors?.[0] || 'Error al guardar el usuario');
        }
    },

    eliminar: async function(id) {
        if (!confirm('¿Eliminar este usuario?')) return;

        const csrf = appUsuario.getCsrfToken();
        const resp = await fetch('<?= base_url('usuario/eliminar') ?>/' + id, { 
            method: 'POST',
            headers: csrf ? { 'X-CSRF-TOKEN': csrf } : {}
        });
        if (resp.ok) {
            this.listar();
        } else {
            const txt = await resp.text();
            alert('Error al eliminar: ' + txt);
        }
    },

    buscar: function() {
        this.paginaActual = 1;
        this.filtro = document.getElementById('txtBuscarUsuario').value.trim();
        this.listar();
    },

    paginacionUsuarios: function(pager) {
        const cont = document.getElementById('paginacion-usuarios');
        cont.innerHTML = '';

        if (!pager || pager.total <= 1) {
            return;
        }

        const current = pager.current;
        if (current > 1) {
            cont.innerHTML += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="appUsuario.paginaActual=1; appUsuario.listar();">Primero</a></li>`;
            cont.innerHTML += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="appUsuario.paginaActual=${current-1}; appUsuario.listar();">Anterior</a></li>`;
        } else {
            cont.innerHTML += `<li class="page-item disabled"><span class="page-link">Primero</span></li>`;
            cont.innerHTML += `<li class="page-item disabled"><span class="page-link">Anterior</span></li>`;
        }

        for (let i = 1; i <= pager.total; i++) {
            cont.innerHTML += `<li class="page-item ${i===current ? 'active' : ''}"><a class="page-link" href="javascript:void(0)" onclick="appUsuario.paginaActual=${i}; appUsuario.listar();">${i}</a></li>`;
        }

        if (current < pager.total) {
            cont.innerHTML += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="appUsuario.paginaActual=${current+1}; appUsuario.listar();">Siguiente</a></li>`;
            cont.innerHTML += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="appUsuario.paginaActual=${pager.total}; appUsuario.listar();">Último</a></li>`;
        } else {
            cont.innerHTML += `<li class="page-item disabled"><span class="page-link">Siguiente</span></li>`;
            cont.innerHTML += `<li class="page-item disabled"><span class="page-link">Último</span></li>`;
        }
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

window.moduleInit = window.moduleInit || function() { if(window.appUsuario) window.appUsuario.init(); };
</script>

<script>
// Permisos del usuario actual para este módulo
window.permisosUsuario = {
    bitConsulta: <?= $permisos['bitConsulta'] ?>,
    bitAgregar: <?= $permisos['bitAgregar'] ?>,
    bitEditar: <?= $permisos['bitEditar'] ?>,
    bitEliminar: <?= $permisos['bitEliminar'] ?>,
    bitDetalle: <?= $permisos['bitDetalle'] ?>
};
</script>