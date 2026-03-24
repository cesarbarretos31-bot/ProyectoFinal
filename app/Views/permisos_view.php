<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Seguridad</a></li>
    <li class="breadcrumb-item active">Permisos Perfil</li>
  </ol>
</nav>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5>Configuración de Permisos por Perfil</h5>
        <select id="selectPerfil" class="form-select w-25" onchange="cargarPermisosTabla()">
            <option value="">Seleccione un Perfil...</option>
            </select>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Módulo</th>
                    <th>Consulta</th>
                    <th>Agregar</th>
                    <th>Editar</th>
                    <th>Eliminar</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody id="tablaPermisosBody">
                </tbody>
        </table>
    </div>
</div>

<script>
async function cargarPermisosTabla() {
    const idPerfil = document.getElementById('selectPerfil').value;
    if(!idPerfil) return;

    const response = await fetch(`/permisosperfil/mostrar/${idPerfil}`, {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    const permisos = await response.json();

    let html = '';
    permisos.forEach(p => {
        html += `
            <tr>
                <td>${p.strNombreModulo}</td>
                <td>${crearSwitch(p.id, 'bitConsulta', p.bitConsulta)}</td>
                <td>${crearSwitch(p.id, 'bitAgregar', p.bitAgregar)}</td>
                <td>${crearSwitch(p.id, 'bitEditar', p.bitEditar)}</td>
                <td>${crearSwitch(p.id, 'bitEliminar', p.bitEliminar)}</td>
                <td>${crearSwitch(p.id, 'bitDetalle', p.bitDetalle)}</td>
            </tr>`;
    });
    document.getElementById('tablaPermisosBody').innerHTML = html;
}

function crearSwitch(id, campo, valor) {
    const checked = valor == 1 ? 'checked' : '';
    return `
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" ${checked} 
                onchange="cambiarPermiso(${id}, '${campo}', this.checked)">
        </div>`;
}

async function cambiarPermiso(id, campo, estado) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('campo', campo);
    formData.append('valor', estado ? 1 : 0);

    await fetch('/permisosperfil/actualizar', {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` },
        body: formData
    });
    
    // Al actualizar, el menú dinámico se verá afectado la próxima vez que cargue
}
</script>