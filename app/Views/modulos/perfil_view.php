<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between py-3">
        <h5 class="mb-0 fw-bold">Gestión de Perfiles</h5>
        <button class="btn btn-primary btn-sm" onclick="abrirModalNuevo()">Nuevo Perfil</button>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr><th>ID</th><th>Nombre</th><th>Admin</th><th class="text-center">Acciones</th></tr>
            </thead>
            <tbody id="tablaPerfiles"></tbody>
        </table>
        <div id="paginacionContainer" class="d-flex justify-content-center"></div>
    </div>
</div>

<div class="modal fade" id="modalPerfil" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form id="formPerfil">
            <div class="modal-body">
                <input type="text" name="strNombrePerfil" class="form-control mb-2" placeholder="Nombre" required>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="bitAdministrador" value="1">
                    <label class="form-check-label">¿Es Administrador?</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div></div>
</div>

<script>
(function() {
    const bsModal = new bootstrap.Modal(document.getElementById('modalPerfil'));

    async function cargarPerfiles(p = 1) {
        try {
            const response = await fetch(`<?= base_url('perfil') ?>?page=${p}`);
            let text = await response.text();
            
            // LIMPIEZA: Si hay basura de debug al final, la cortamos
            if (text.includes('/g, "");
                // Re-bind links
                document.querySelectorAll('#paginacionContainer a').forEach(a => {
                    const url = new URL(a.href);
                    const page = url.searchParams.get('page');
                    a.href = "#";
                    a.onclick = () => cargarPerfiles(page);
                });
            }
        } catch (e) { console.error(e); }
    }

    window.abrirModalNuevo = () => { document.getElementById('formPerfil').reset(); bsModal.show(); };
    
    document.getElementById('formPerfil').onsubmit = async (e) => {
        e.preventDefault();
        await fetch('<?= base_url("perfil/crear") ?>', { method: 'POST', body: new FormData(e.target) });
        bsModal.hide();
        cargarPerfiles();
    };

    window.borrar = async (id) => {
        if(confirm('¿Seguro?')) {
            await fetch(`<?= base_url("perfil/eliminar") ?>/${id}`, { method: 'DELETE' });
            cargarPerfiles();
        }
    };

    cargarPerfiles();
})();
</script>