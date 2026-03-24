<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Seguridad</a></li>
        <li class="breadcrumb-item active" aria-current="page">Perfil</li>
    </ol>
</nav>

<div class="card shadow-sm border-0 animate__animated animate__fadeIn">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2"></i>Gestión de Perfiles</h5>
        <button class="btn btn-primary btn-sm" onclick="nuevoPerfil()">
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
        <div id="paginacion" class="d-flex justify-content-center mt-3"></div>
    </div>
</div>

<div class="modal fade" id="modalPerfil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitulo">Nuevo Perfil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPerfil">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre del Perfil</label>
                        <input type="text" name="strNombrePerfil" class="form-control" placeholder="Ej: Supervisor" required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="bitAdministrador" id="checkAdmin">
                        <label class="form-check-label small" for="checkAdmin">¿Tiene privilegios de Administrador?</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm">Guardar Perfil</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Usamos un objeto para no chocar con otras variables globales
const ModuloPerfil = {
    paginaActual: 1,
    modal: null,

    init: function() {
        this.modal = new bootstrap.Modal(document.getElementById('modalPerfil'));
        this.cargarPerfiles();
        this.setupForm();
    },

    cargarPerfiles: async function(pagina = 1) {
        this.paginaActual = pagina;
        try {
            const response = await fetch(`<?= base_url('perfil') ?>?page=${pagina}`);
            const res = await response.json();
            
            // 1. Renderizar Tabla
            let html = '';
            if (res.perfiles.length > 0) {
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
                                <button class="btn btn-outline-warning btn-sm" onclick="editarPerfil(${p.id})"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-outline-danger btn-sm" onclick="ModuloPerfil.eliminar(${p.id})"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>`;
                });
            } else {
                html = '<tr><td colspan="4" class="text-center">No hay registros disponibles.</td></tr>';
            }
            document.getElementById('tablaPerfiles').innerHTML = html;

            // 2. Renderizar Paginación (Limpiando DEBUG de CI4)
            if (res.pager) {
                let pagerHtml = res.pager.replace(//g, ""); // Limpia Debug CI4
                const divPaginacion = document.getElementById('paginacion');
                divPaginacion.innerHTML = pagerHtml;

                // Estilizar y capturar clics de los links de paginación
                divPaginacion.querySelectorAll('a').forEach(link => {
                    link.classList.add('page-link');
                    link.parentElement.classList.add('page-item');
                    if(link.parentElement.tagName === 'LI' && link.href.includes('page=' + pagina)) {
                         link.parentElement.classList.add('active');
                    }
                    link.onclick = (e) => {
                        e.preventDefault();
                        const url = new URL(link.href);
                        this.cargarPerfiles(url.searchParams.get('page'));
                    };
                });
                divPaginacion.querySelector('ul')?.classList.add('pagination', 'pagination-sm');
            }
        } catch (err) {
            console.error("Error:", err);
        }
    },

    setupForm: function() {
        document.getElementById('formPerfil').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            try {
                const response = await fetch('<?= base_url("perfil/crear") ?>', {
                    method: 'POST',
                    body: formData
                });
                const res = await response.json();
                
                if (response.ok) {
                    this.modal.hide();
                    e.target.reset();
                    this.cargarPerfiles(this.paginaActual);
                } else {
                    alert(res.msg || "Error al guardar");
                }
            } catch (err) {
                alert("Error de conexión");
            }
        };
    },

    eliminar: async function(id) {
        if (!confirm('¿Seguro que deseas eliminar este perfil?')) return;
        
        try {
            const response = await fetch(`<?= base_url("perfil/eliminar") ?>/${id}`, {
                method: 'DELETE'
            });
            if (response.ok) {
                this.cargarPerfiles(this.paginaActual);
            } else {
                const res = await response.json();
                alert(res.msg || "No se puede eliminar (posiblemente tiene usuarios asignados)");
            }
        } catch (err) {
            alert("Error al intentar eliminar");
        }
    }
};

// Funciones globales que llaman al objeto (para los onclick del HTML)
function nuevoPerfil() { ModuloPerfil.modal.show(); }
ModuloPerfil.init();
</script>