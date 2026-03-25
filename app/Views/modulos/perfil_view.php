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
<script>
(function() {
    // Forzamos la URL de tu servidor en Railway para evitar el error de consola 
    const API_URL = "https://proyectofinal-production-e9e1.up.railway.app/index.php/perfil";
    const modalEl = document.getElementById('modalPerfil');
    const bsModal = new bootstrap.Modal(modalEl);

    async function cargarPerfiles(pagina = 1) {
        const tabla = document.getElementById('tablaPerfiles');
        const paginador = document.getElementById('paginacionContainer');

        try {
            // Requisito: Uso de Fetch API 
            const response = await fetch(`${API_URL}?page=${pagina}`);
            const res = await response.json();

            let html = '';
            // Manipulación de objetos DOM para renderizar 
            if (res.perfiles && res.perfiles.length > 0) {
                res.perfiles.forEach(p => {
                    html += `
                        <tr>
                            <td>${p.id}</td>
                            <td>${p.strNombrePerfil}</td>
                            <td>${p.bitAdministrador == '1' ? 'SÍ' : 'NO'}</td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm" onclick="borrarPerfil(${p.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>`;
                });
            } else {
                html = '<tr><td colspan="4" class="text-center">No hay registros</td></tr>';
            }
            
            tabla.innerHTML = html;
            if (paginador) paginador.innerHTML = res.pager || '';

            // Hacer que los links de la paginación no recarguen la página (SPA Style)
            document.querySelectorAll('#paginacionContainer a').forEach(link => {
                link.onclick = (e) => {
                    e.preventDefault();
                    const url = new URL(link.href);
                    cargarPerfiles(url.searchParams.get('page'));
                };
            });

        } catch (err) {
            console.error("Fallo técnico:", err);
            tabla.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error al conectar con Railway</td></tr>';
        }
    }

    // Funciones globales para botones
    window.borrarPerfil = async (id) => {
        if (!confirm("¿Eliminar?")) return;
        const res = await fetch(`${API_URL}/eliminar/${id}`, { method: 'DELETE' });
        if (res.ok) cargarPerfiles();
    };

    document.getElementById('formPerfil').onsubmit = async (e) => {
        e.preventDefault();
        const res = await fetch(`${API_URL}/crear`, {
            method: 'POST',
            body: new FormData(e.target)
        });
        if (res.ok) { bsModal.hide(); cargarPerfiles(); }
    };

    cargarPerfiles();
})();
</script>