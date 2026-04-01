<div class="container-fluid py-4 fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-shield-lock-fill text-primary me-2"></i> Matriz de Permisos</h4>
        <div class="d-flex align-items-center gap-3">
            <button id="btnGuardarMatriz" class="btn btn-primary btn-sm" onclick="appPermisos.guardarMatriz()" <?= $permisos['bitEditar'] ? '' : 'disabled' ?>>Guardar Matriz de Permisos</button>
            <?php if (!$permisos['bitEditar']): ?>
            <small class="text-muted">(Solo lectura porque no tiene permiso de editar)</small>
            <?php endif; ?>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 mb-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label">Perfil</label>
                    <select id="perfilSelect" class="form-select" onchange="appPermisos.cargar()">
                        <option value="">Seleccione un perfil...</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Buscar módulo</label>
                    <input id="txtBuscarPermisos" class="form-control" placeholder="Filtrar módulo..." onkeyup="appPermisos.buscar()">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Módulo</th>
                            <th class="text-center">Consulta</th>
                            <th class="text-center">Agregar</th>
                            <th class="text-center">Editar</th>
                            <th class="text-center">Eliminar</th>
                            <th class="text-center">Detalle</th>
                        </tr>
                    </thead>
                    <tbody id="permisosBody">
                        <tr><td colspan="6" class="text-center">Seleccione un perfil...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between mt-3">
                <small id="permisosTotal" class="text-muted">0 registros</small>
                <nav><ul class="pagination pagination-sm mb-0" id="permisosPaginacion"></ul></nav>
            </div>
        </div>
    </div>
</div>

<script>
window.appPermisos = {
    paginaActual: 1,
    totalPaginas: 1,
    idPerfilActual: null,
    datosCache: [],

    init: async function() {
        await this.cargarPerfiles();
    },

    cargarPerfiles: async function() {
        const resp = await fetch('<?= base_url('perfil/listar') ?>');
        const data = await resp.json();
        const select = document.getElementById('perfilSelect');
        select.innerHTML = '<option value="">Seleccione un perfil...</option>';
        data.data.forEach(p => {
            select.innerHTML += `<option value="${p.id}">${p.strNombrePerfil}</option>`;
        });
    },

    cargar: async function() {
        const idPerfil = Number(document.getElementById('perfilSelect').value);
        if (!idPerfil) {
            document.getElementById('permisosBody').innerHTML = '<tr><td colspan="6" class="text-center">Seleccione perfil...</td></tr>';
            document.getElementById('permisosTotal').textContent = '0 registros';
            return;
        }

        this.idPerfilActual = idPerfil;
        this.paginaActual = 1;
        await this.actualizarLista();
    },

    actualizarLista: async function() {
        if (!this.idPerfilActual) return;

        const resp = await fetch(`<?= base_url('permisosperfil/mostrar') ?>/${this.idPerfilActual}`);
        const datos = await resp.json();

        if (!Array.isArray(datos)) {
            document.getElementById('permisosBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger">No se pudieron cargar permisos</td></tr>';
            return;
        }

        this.datosCache = datos.map(item => ({
            ...item,
            bitConsulta: Number(item.bitConsulta) || 0,
            bitAgregar: Number(item.bitAgregar) || 0,
            bitEditar: Number(item.bitEditar) || 0,
            bitEliminar: Number(item.bitEliminar) || 0,
            bitDetalle: Number(item.bitDetalle) || 0
        }));

        this.totalPaginas = Math.ceil(this.datosCache.length / 5) || 1;
        this.mostrarPagina();
    },

    buscar: function() {
        this.paginaActual = 1;
        this.mostrarPagina();
    },

    mostrarPagina: function() {
        const term = document.getElementById('txtBuscarPermisos').value.toLowerCase();
        let lista = this.datosCache;

        if (term) {
            lista = lista.filter(r => r.strNombreModulo.toLowerCase().includes(term));
        }

        const desde = (this.paginaActual - 1) * 5;
        const hasta = desde + 5;
        const pagina = lista.slice(desde, hasta);

        const body = document.getElementById('permisosBody');
        if (pagina.length === 0) {
            body.innerHTML = '<tr><td colspan="6" class="text-center">No se encontraron registros</td></tr>';
            document.getElementById('permisosTotal').textContent = `${lista.length} registros`;
            this.cargarPaginacion(lista.length);
            return;
        }

        body.innerHTML = '';
        pagina.forEach(item => {
            body.innerHTML += `
                <tr data-idpermiso="${item.idPermiso || 0}" data-idmodulo="${item.idModulo}">
                    <td>${item.strNombreModulo}</td>
                    <td class="text-center"><input type="checkbox" ${item.bitConsulta ? 'checked' : ''} onchange="appPermisos.togglePermiso(this, 'bitConsulta')"></td>
                    <td class="text-center"><input type="checkbox" ${item.bitAgregar ? 'checked' : ''} onchange="appPermisos.togglePermiso(this, 'bitAgregar')"></td>
                    <td class="text-center"><input type="checkbox" ${item.bitEditar ? 'checked' : ''} onchange="appPermisos.togglePermiso(this, 'bitEditar')"></td>
                    <td class="text-center"><input type="checkbox" ${item.bitEliminar ? 'checked' : ''} onchange="appPermisos.togglePermiso(this, 'bitEliminar')"></td>
                    <td class="text-center"><input type="checkbox" ${item.bitDetalle ? 'checked' : ''} onchange="appPermisos.togglePermiso(this, 'bitDetalle')"></td>
                </tr>`;
        });

        document.getElementById('permisosTotal').textContent = `${lista.length} registros`;
        this.cargarPaginacion(lista.length);
    },

    cargarPaginacion: function(total) {
        const totalPag = Math.ceil(total / 5) || 1;
        this.totalPaginas = totalPag;
        const cont = document.getElementById('permisosPaginacion');
        cont.innerHTML = '';

        for (let i = 1; i <= totalPag; i++) {
            cont.innerHTML += `<li class="page-item ${i === this.paginaActual ? 'active' : ''}"><a class="page-link" href="javascript:void(0)" onclick="appPermisos.paginaActual=${i}; appPermisos.mostrarPagina();">${i}</a></li>`;
        }
    },

    togglePermiso: async function(element, campo) {
        const row = element.closest('tr');
        const idPermiso = Number(row.dataset.idpermiso);
        const idModulo = Number(row.dataset.idmodulo);
        const idPerfil = this.idPerfilActual;
        const valor = element.checked ? 1 : 0;

        if (!idPerfil) return;

        if (idPermiso > 0) {
            await this.actualizarPermiso(idPermiso, campo, valor);
            return;
        }

        // si no existe registro, crear registro completo con el estado actual de la fila
        const checkbox = (key) => row.querySelector(`input[onchange*="${key}"]`)?.checked ? 1 : 0;
        const data = new URLSearchParams();
        data.append('idPerfil', idPerfil);
        data.append('idModulo', idModulo);
        data.append('bitConsulta', checkbox('bitConsulta'));
        data.append('bitAgregar', checkbox('bitAgregar'));
        data.append('bitEditar', checkbox('bitEditar'));
        data.append('bitEliminar', checkbox('bitEliminar'));
        data.append('bitDetalle', checkbox('bitDetalle'));

        const resp = await fetch('<?= base_url('permisosperfil/guardar') ?>', {
            method: 'POST',
            body: data,
        });

        if (resp.ok) {
            const result = await resp.json();
            row.dataset.idpermiso = result.id || idPermiso;
            this.cargar();
        } else {
            console.error('Error creando los permisos', await resp.text());
            alert('Error creando permisos');
        }
    },

    actualizarPermiso: async function(idPermiso, campo, valor) {
        const data = new URLSearchParams();
        data.append('id', idPermiso);
        data.append('campo', campo);
        data.append('valor', valor);

        const resp = await fetch('<?= base_url('permisosperfil/actualizar') ?>', {
            method: 'POST',
            body: data,
        });

        if (!resp.ok) {
            console.error('No se pudo actualizar el permiso', await resp.text());
            alert('Error al actualizar permiso');
        }
    },

    guardarMatriz: async function() {
        if (!this.idPerfilActual) {
            alert('Seleccione primero un perfil');
            return;
        }

        if (!<?= $permisos['bitEditar'] ? 'true' : 'false' ?>) {
            alert('No tienes permiso para editar esta matriz.');
            return;
        }

        const rows = document.querySelectorAll('#permisosBody tr');
        for (const row of rows) {
            const idPermiso = Number(row.dataset.idpermiso);
            const idModulo = Number(row.dataset.idmodulo);
            if (!idModulo) continue;

            const bitConsulta = row.querySelector('input[onchange*="bitConsulta"]')?.checked ? 1 : 0;
            const bitAgregar = row.querySelector('input[onchange*="bitAgregar"]')?.checked ? 1 : 0;
            const bitEditar = row.querySelector('input[onchange*="bitEditar"]')?.checked ? 1 : 0;
            const bitEliminar = row.querySelector('input[onchange*="bitEliminar"]')?.checked ? 1 : 0;
            const bitDetalle = row.querySelector('input[onchange*="bitDetalle"]')?.checked ? 1 : 0;

            if (idPermiso > 0) {
                const payload = new URLSearchParams();
                payload.append('id', idPermiso);
                payload.append('campo', 'bitConsulta');
                payload.append('valor', bitConsulta);
                await fetch('<?= base_url('permisosperfil/actualizar') ?>', { method: 'POST', body: payload });

                payload.set('campo', 'bitAgregar');
                payload.set('valor', bitAgregar);
                await fetch('<?= base_url('permisosperfil/actualizar') ?>', { method: 'POST', body: payload });

                payload.set('campo', 'bitEditar');
                payload.set('valor', bitEditar);
                await fetch('<?= base_url('permisosperfil/actualizar') ?>', { method: 'POST', body: payload });

                payload.set('campo', 'bitEliminar');
                payload.set('valor', bitEliminar);
                await fetch('<?= base_url('permisosperfil/actualizar') ?>', { method: 'POST', body: payload });

                payload.set('campo', 'bitDetalle');
                payload.set('valor', bitDetalle);
                await fetch('<?= base_url('permisosperfil/actualizar') ?>', { method: 'POST', body: payload });
            } else {
                const payload = new URLSearchParams();
                payload.append('idPerfil', this.idPerfilActual);
                payload.append('idModulo', idModulo);
                payload.append('bitConsulta', bitConsulta);
                payload.append('bitAgregar', bitAgregar);
                payload.append('bitEditar', bitEditar);
                payload.append('bitEliminar', bitEliminar);
                payload.append('bitDetalle', bitDetalle);

                await fetch('<?= base_url('permisosperfil/guardar') ?>', { method: 'POST', body: payload });
            }
        }

        alert('Matriz de permisos guardada correctamente.');
        this.cargar();
    }
};

window.moduleInit = window.moduleInit || function() { if(window.appPermisos) window.appPermisos.init(); };
</script>

<script>
// Permisos del usuario actual para este módulo
window.permisosPermisosPerfil = {
    bitConsulta: <?= $permisos['bitConsulta'] ?>,
    bitAgregar: <?= $permisos['bitAgregar'] ?>,
    bitEditar: <?= $permisos['bitEditar'] ?>,
    bitEliminar: <?= $permisos['bitEliminar'] ?>,
    bitDetalle: <?= $permisos['bitDetalle'] ?>
};
</script>