<div class="container-fluid py-4 fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-shield-lock text-primary me-2"></i> Gestión de Perfiles</h4>
            <small class="text-muted" id="total-registros">Cargando...</small>
        </div>
        <button class="btn btn-primary btn-sm shadow-sm" onclick="prepararNuevo()">
            <i class="bi bi-plus-lg"></i> + Nuevo Perfil
        </button>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0" id="txtBuscar" 
                       placeholder="Buscar por nombre... (búsqueda automática)" onkeyup="buscarTiempoReal()">
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Perfil</th>
                        <th>Administrador</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-perfiles" class="small">
                    </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <nav class="d-flex justify-content-center">
                <ul class="pagination pagination-sm mb-0" id="paginacion-controles"></ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPerfil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTitulo">Nuevo Perfil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPerfil">
                <div class="modal-body">
                    <input type="hidden" name="id" id="perfil_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Perfil</label>
                        <input type="text" name="strNombrePerfil" id="strNombrePerfil" class="form-control" required>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="bitAdministrador" id="bitAdministrador" value="1">
                        <label class="form-check-label">Privilegios de Administrador</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Datos</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
// Variables de estado
let paginaActual = 1;
let timeoutBusqueda;
const modalElement = document.getElementById('modalPerfil');
const bsModal = new bootstrap.Modal(modalElement);

// --- FUNCIONALIDAD: BÚSQUEDA AUTOMÁTICA ---
function buscarTiempoReal() {
    clearTimeout(timeoutBusqueda);
    timeoutBusqueda = setTimeout(() => {
        paginaActual = 1; 
        listarPerfiles();
    }, 400); 
}

// --- CRUD: LISTAR (READ) ---
async function listarPerfiles() {
    const busqueda = document.getElementById('txtBuscar').value;
    try {
        const resp = await fetch(`<?= base_url('perfiles/listar') ?>?page=${paginaActual}&search=${busqueda}`);
        const res = await resp.json();
        
        const tbody = document.getElementById('tbody-perfiles');
        tbody.innerHTML = '';

        res.data.forEach(p => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${p.id}</td>
                <td class="fw-bold">${p.strNombrePerfil} </td>
                <td>${p.bitAdministrador == 1 ? '<span class="badge bg-success-subtle text-success">Sí</span>' : '<span class="badge bg-light text-muted">No</span>'}</td>
                <td class="text-end">
                    <button class="btn btn-link text-warning p-1" onclick="prepararEditar(${p.id}, '${p.strNombrePerfil}', ${p.bitAdministrador})"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-link text-danger p-1" onclick="eliminarPerfil(${p.id})"><i class="bi bi-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        actualizarPaginacion(res.pager);
        document.getElementById('total-registros').innerText = `${res.pager.totalRows} registros encontrados`;
    } catch (err) { console.error("Error al listar:", err); }
}

// --- CRUD: GUARDAR (CREATE / UPDATE) ---
document.getElementById('formPerfil').onsubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    const resp = await fetch('<?= base_url("perfiles/guardar") ?>', {
        method: 'POST',
        body: formData
    });
    
    const res = await resp.json();
    if(res.status === 'success') {
        bsModal.hide();
        listarPerfiles();
    }
};

// --- CRUD: ELIMINAR (DELETE) ---
async function eliminarPerfil(id) {
    if(!confirm('¿Seguro que desea eliminar este perfil? [cite: 15]')) return;
    
    const resp = await fetch(`<?= base_url('perfiles/eliminar') ?>/${id}`, { method: 'DELETE' });
    const res = await resp.json();
    if(res.status === 'success') listarPerfiles();
}

// --- UTILIDADES DE UI ---
function prepararNuevo() {
    document.getElementById('modalTitulo').innerText = "Nuevo Perfil";
    document.getElementById('formPerfil').reset();
    document.getElementById('perfil_id').value = "";
    bsModal.show();
}

function prepararEditar(id, nombre, admin) {
    document.getElementById('modalTitulo').innerText = "Editar Perfil";
    document.getElementById('perfil_id').value = id;
    document.getElementById('strNombrePerfil').value = nombre;
    document.getElementById('bitAdministrador').checked = (admin == 1);
    bsModal.show();
}

function actualizarPaginacion(pager) {
    const cont = document.getElementById('paginacion-controles');
    cont.innerHTML = '';
    
    for (let i = 1; i <= pager.total; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === pager.current ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="javascript:void(0)" onclick="paginaActual=${i}; listarPerfiles();">${i}</a>`;
        cont.appendChild(li);
    }
}

// Carga Inicial
listarPerfiles();
</script>