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
    const modal = new bootstrap.Modal(document.getElementById('modalPerfil'));
    const base = "<?= base_url('index.php') ?>"; // Ruta con index.php para Railway

    async function listar(p = 1) {
        try {
            const res = await fetch(`${base}/perfil?page=${p}`);
            const data = await res.json();
            let html = '';
            data.perfiles.forEach(row => {
                html += `<tr>
                    <td>${row.id}</td>
                    <td>${row.strNombrePerfil}</td>
                    <td>${row.bitAdministrador == 1 ? 'SÍ' : 'NO'}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-danger" onclick="eliminar(${row.id})">Borrar</button>
                    </td>
                </tr>`;
            });
            document.getElementById('tablaPerfiles').innerHTML = html || '<tr><td colspan="4">Sin datos</td></tr>';
            document.getElementById('paginacionContainer').innerHTML = data.pager || '';
            
            // Hacer que la paginación use Fetch
            document.querySelectorAll('#paginacionContainer a').forEach(a => {
                const url = new URL(a.href);
                const page = url.searchParams.get('page');
                a.onclick = (e) => { e.preventDefault(); listar(page); };
                a.href = "#";
            });
        } catch (e) { console.error(e); }
    }

    window.abrirModal = () => { document.getElementById('formPerfil').reset(); modal.show(); };

    document.getElementById('formPerfil').onsubmit = async (e) => {
        e.preventDefault();
        const res = await fetch(`${base}/perfil/crear`, { method: 'POST', body: new FormData(e.target) });
        if (res.ok) { modal.hide(); listar(); }
    };

    window.eliminar = async (id) => {
        if (!confirm('¿Seguro?')) return;
        const res = await fetch(`${base}/perfil/eliminar/${id}`, { method: 'DELETE' });
        if (res.ok) listar();
    };

    listar();
})();
</script>