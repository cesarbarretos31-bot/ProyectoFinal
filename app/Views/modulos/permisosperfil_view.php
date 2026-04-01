<div class="container-fluid py-4 fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-shield-lock-fill text-primary me-2"></i> Permisos por Perfil</h4>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <select id="perfilSelect" class="form-select" onchange="appPermisos.cargar()">
                        <option value="">Seleccione un perfil...</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="bg-light text-muted">
                        <tr><th>Módulo</th><th>Consulta</th><th>Agregar</th><th>Editar</th><th>Eliminar</th><th>Detalle</th></tr>
                    </thead>
                    <tbody id="permisosBody"><tr><td colspan="6" class="text-center">Seleccione perfil...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
window.appPermisos = {
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
        const idPerfil = document.getElementById('perfilSelect').value;
        if (!idPerfil) {
            document.getElementById('permisosBody').innerHTML = '<tr><td colspan="6" class="text-center">Seleccione perfil...</td></tr>';
            return;
        }

        const resp = await fetch('<?= base_url('permisosperfil/mostrar') ?>/' + idPerfil);
        const datos = await resp.json();
        const body = document.getElementById('permisosBody');
        body.innerHTML = '';

        datos.forEach(item => {
            body.innerHTML += `<tr><td>${item.strNombreModulo}</td><td>${this.check(item.bitConsulta)}</td><td>${this.check(item.bitAgregar)}</td><td>${this.check(item.bitEditar)}</td><td>${this.check(item.bitEliminar)}</td><td>${this.check(item.bitDetalle)}</td></tr>`;
        });
    },

    check: function(flag) {
        return flag ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>';
    }
};

window.moduleInit = window.moduleInit || function() { if(window.appPermisos) window.appPermisos.init(); };
</script>