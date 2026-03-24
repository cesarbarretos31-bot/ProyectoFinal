<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Seguridad</a></li>
    <li class="breadcrumb-item active" aria-current="page">Perfil</li>
  </ol>
</nav>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Gestión de Perfiles</h5>
        <button class="btn btn-sm btn-primary" onclick="nuevoPerfil()">Nuevo Perfil</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Perfil</th>
                        <th>Admin</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaPerfiles">
                    </tbody>
            </table>
        </div>
        <div id="paginacion" class="mt-3"></div>
    </div>
</div>

<script>
async function cargarPerfiles(pagina = 1) {
    const token = localStorage.getItem('token');
    try {
        const response = await fetch(`/perfil?page=${pagina}`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const res = await response.json();
        
        let html = '';
        res.perfiles.forEach(p => {
            html += `
                <tr>
                    <td>${p.id}</td>
                    <td>${p.strNombrePerfil}</td>
                    <td>${p.bitAdministrador == 1 ? 'SÍ' : 'NO'}</td>
                    <td>
                        <button class="btn btn-warning btn-sm">Editar</button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarPerfil(${p.id})">Eliminar</button>
                    </td>
                </tr>`;
        });
        document.getElementById('tablaPerfiles').innerHTML = html;
        // Aquí renderizarías la paginación de res.pager
    } catch (err) {
        console.error("Error al cargar perfiles", err);
    }
}

// Llamar al cargar la página
cargarPerfiles();
</script>