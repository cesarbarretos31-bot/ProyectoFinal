<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Seguridad</a></li>
        <li class="breadcrumb-item active" aria-current="page">Perfil</li>
    </ol>
</nav>

<div class="card shadow-sm border-0 animate__animated animate__fadeIn">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-person-badge me-2"></i>Gestión de Perfiles</h5>
        <button class="btn btn-primary btn-sm" onclick="abrirModalNuevo()">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Perfil
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Nombre Perfil</th>
                        <th>Administrador</th>
                        <th class="text-center" style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaPerfiles">
                    <tr><td colspan="4" class="text-center p-4">Cargando datos...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="paginacionContainer" class="d-flex justify-content-center mt-3"></div>
    </div>
</div>

<div class="modal fade" id="modalPerfil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Nuevo Perfil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPerfil">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre del Perfil</label>
                        <input type="text" name="strNombrePerfil" class="form-control" placeholder="Ej: Gerente" required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="bitAdministrador" value="1" id="checkAdmin">
                        <label class="form-check-label small" for="checkAdmin">¿Es Administrador?</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Encapsulamos para evitar conflictos
(function() {
    let paginaActual = 1;
    const modalElement = document.getElementById('modalPerfil');
    const bsModal = new bootstrap.Modal(modalElement);

    window.abrirModalNuevo = () => {
        document.getElementById('formPerfil').reset();
        bsModal.show();
    };

    async function cargarPerfiles(pagina = 1) {
        paginaActual = pagina;
        try {
            const response = await fetch(`<?= base_url('perfil') ?>?page=${pagina}`);
            const res = await response.json();
            
            // 1. Renderizar Filas
            let html = '';
            if (res.perfiles && res.perfiles.length > 0) {
                res.perfiles.forEach(p => {
                    html += `
                        <tr>
                            <td><span class="badge bg-light text-dark border">${p.id}</span></td>
                            <td class="fw-semibold">${p.strNombrePerfil}</td>
                            <td>
                                <span class="badge ${p.bitAdministrador == 1 ? 'bg-success' : 'bg-secondary'}">
                                    ${p.bitAdministrador == 1 ? 'SÍ' : 'NO'}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-outline-danger btn-sm" onclick="borrarPerfil(${p.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>`;
                });
            } else {
                html = '<tr><td colspan="4" class="text-center">No hay registros</td></tr>';
            }
            document.getElementById('tablaPerfiles').innerHTML = html;

            // 2. Renderizar Paginación (Limpiando DEBUG de CI4)
            if (res.pager) {
                let pagerHtml = res.pager.replace(//g, ""); // Limpia los comentarios de Debug
                const container = document.getElementById('paginacionContainer');
                container.innerHTML = pagerHtml;

                // Estilizar links para que parezcan de Bootstrap
                container.querySelectorAll('ul').forEach(ul => ul.classList.add('pagination', 'pagination-sm'));
                container.querySelectorAll('li').forEach(li => li.classList.add('page-item'));
                container.querySelectorAll('a').forEach(a => {
                    a.classList.add('page-link');
                    a.onclick = (e) => {
                        e.preventDefault();
                        const url = new URL(a.href);
                        cargarPerfiles(url.searchParams.get('page'));
                    };
                });
            }
        } catch (err) {
            console.error(err);
        }
    }

    // Guardar Perfil
    document.getElementById('formPerfil').onsubmit = async (e) => {
        e.preventDefault();
        const data = new FormData(e.target);
        const res = await fetch('<?= base_url("perfil/crear") ?>', { method: 'POST', body: data });
        if (res.ok) {
            bsModal.hide();
            cargarPerfiles(paginaActual);
        } else {
            alert("Error al guardar");
        }
    };

    // Eliminar Perfil
    window.borrarPerfil = async (id) => {
        if (!confirm("¿Deseas eliminar este perfil?")) return;
        const res = await fetch(`<?= base_url("perfil/eliminar") ?>/${id}`, { method: 'DELETE' });
        if (res.ok) {
            cargarPerfiles(paginaActual);
        } else {
            alert("No se pudo eliminar. Puede que tenga usuarios asociados.");
        }
    };

    cargarPerfiles();
})();
</script>