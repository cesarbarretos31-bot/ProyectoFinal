<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Seguridad</a></li>
        <li class="breadcrumb-item active">Perfil</li>
    </ol>
</nav>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Gestión de Perfiles</h5>
        <button class="btn btn-primary btn-sm" onclick="abrirModal()">Nuevo Perfil</button>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Perfil</th>
                    <th>Admin</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaPerfiles">
                <tr><td colspan="4" class="text-center">Cargando...</td></tr>
            </tbody>
        </table>
        <div id="paginacionContainer" class="d-flex justify-content-center"></div>
    </div>
</div>

<div class="modal fade" id="modalPerfil" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Registrar Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPerfil">
                <div class="modal-body">
                    <input type="text" name="strNombrePerfil" class="form-control mb-3" placeholder="Nombre" required>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="bitAdministrador" value="1">
                        <label class="form-check-label">¿Es Administrador?</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    const URL_API = "https://proyectofinal-production-e9e1.up.railway.app/index.php/perfil";

    async function cargarTabla(p = 1) {
        const tbody = document.getElementById('tablaPerfiles');
        try {
            const res = await fetch(`${URL_API}?page=${p}`); [cite: 36]
            const data = await res.json();
            
            let filas = '';
            data.perfiles.forEach(p => {
                filas += `<tr>
                    <td>${p.id}</td>
                    <td>${p.strNombrePerfil}</td>
                    <td>${p.bitAdministrador == '1' ? 'SÍ' : 'NO'}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-danger" onclick="borrar(${p.id})">Eliminar</button>
                    </td>
                </tr>`;
            });
            tbody.innerHTML = filas || '<tr><td colspan="4">No hay datos</td></tr>';
            
            // Inyectamos paginación de 5 filas 
            const pag = document.getElementById('paginacionContainer');
            if (pag) pag.innerHTML = data.pager || '';
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="4">Error al procesar JSON</td></tr>';
        }
    }
    cargarTabla();
})();
</script>
