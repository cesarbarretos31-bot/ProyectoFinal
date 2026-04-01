<div class="container-fluid py-4 fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-people text-primary me-2"></i> Gestión de Usuarios</h4>
        </div>
        <button class="btn btn-primary btn-sm" onclick="appUsuario.prepararNuevo()">+ Nuevo Usuario</button>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th>ID</th>
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
                            <input type="text" name="strNombreUsuario" id="strNombreUsuario" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Perfil</label>
                            <select name="idPerfil" id="idPerfil" class="form-select" required></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo</label>
                            <input type="email" name="strCorreo" id="strCorreo" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="strNumeroCelular" id="strNumeroCelular" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="strPwd" id="strPwd" class="form-control" required>
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
        const resp = await fetch('<?= base_url('usuario/listar') ?>');
        const usuarios = await resp.json();
        const tbody = document.getElementById('tbody-usuarios');
        tbody.innerHTML = '';

        if (!usuarios.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No hay usuarios.</td></tr>';
            return;
        }

        usuarios.forEach(u => {
            tbody.innerHTML += `<tr><td>${u.id}</td><td>${u.strNombreUsuario}</td><td>${u.perfil||'--'}</td><td>${u.idEstado==1 ? 'Activo' : 'Inactivo'}</td><td class="text-end"><button class="btn btn-sm btn-warning me-1" onclick="appUsuario.editar(${u.id})">Editar</button><button class="btn btn-sm btn-danger" onclick="appUsuario.eliminar(${u.id})">Eliminar</button></td></tr>`;
        });
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
        const resp = await fetch('<?= base_url('usuario') ?>/' + id, { 
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

window.moduleInit = window.moduleInit || function() { if(window.appUsuario) window.appUsuario.init(); };
</script>