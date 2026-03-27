<div class="input-group mb-3">
    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
    <input type="text" id="txtBuscar" class="form-control" placeholder="Buscar perfil..." onkeyup="buscarTiempoReal()">
</div>

<table class="table align-middle">
    <thead class="bg-light">
        <tr>
            <th>#</th>
            <th>Nombre Perfil</th>
            <th>Admin</th>
            <th class="text-end">Acciones</th>
        </tr>
    </thead>
    <tbody id="tabla-cuerpo"></tbody>
</table>
<div id="paginacion-info" class="text-center"></div>

<script>
let timeoutBusqueda;
let paginaActual = 1;

// 1. FILTRO DE BÚSQUEDA EN TIEMPO REAL
function buscarTiempoReal() {
    clearTimeout(timeoutBusqueda);
    timeoutBusqueda = setTimeout(() => {
        paginaActual = 1; // Reiniciar a la primera página al buscar
        obtenerDatos();
    }, 500); // Espera 500ms tras dejar de escribir
}

// 2. LEER (READ) con Fetch API
async function obtenerDatos() {
    const buscar = document.getElementById('txtBuscar').value;
    const res = await fetch(`<?= base_url('perfiles/listar') ?>?page=${paginaActual}&search=${buscar}`);
    const json = await res.json();
    
    renderizarTabla(json.data);
    renderizarPaginador(json.pager);
}

function renderizarTabla(datos) {
    const tbody = document.getElementById('tabla-cuerpo');
    tbody.innerHTML = datos.map(p => `
        <tr>
            <td>${p.id}</td>
            <td class="fw-bold">${p.strNombrePerfil}</td>
            <td><span class="badge ${p.bitAdministrador == 1 ? 'bg-primary' : 'bg-secondary'}">
                ${p.bitAdministrador == 1 ? 'Sí' : 'No'}</span></td>
            <td class="text-end">
                <button class="btn btn-sm btn-link text-warning" onclick="editarPerfil(${p.id}, '${p.strNombrePerfil}')"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-link text-danger" onclick="eliminarPerfil(${p.id})"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    `).join('');
}

// 3. ELIMINAR (DELETE)
async function eliminarPerfil(id) {
    if (!confirm('¿Desea eliminar este registro?')) return;
    
    const res = await fetch(`<?= base_url('perfiles/eliminar') ?>/${id}`, { method: 'DELETE' });
    const json = await res.json();
    if (json.status === 'success') obtenerDatos();
}

// 4. PAGINACIÓN DINÁMICA
function renderizarPaginador(pager) {
    const pContainer = document.getElementById('paginacion-info');
    pContainer.innerHTML = `Página ${pager.current} de ${pager.total} (Total: ${pager.totalRows} registros)`;
    // Aquí puedes añadir botones de "Anterior" y "Siguiente" que cambien la variable paginaActual
}

// Carga inicial
obtenerDatos();
</script>