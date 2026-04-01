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
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <button id="btnGuardarMatriz" class="btn btn-primary" onclick="guardarMatriz()">Guardar Matriz de Permisos</button>
            <span id="mensajeMatriz" class="text-muted">Ajusta los switches y presiona guardar.</span>
        </div>
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
            <tr data-idmodulo="${p.idModulo}" data-idpermiso="${p.idPermiso}">
                <td>${p.strNombreModulo}</td>
                <td>${crearSwitch(p.idPermiso, 'bitConsulta', p.bitConsulta)}</td>
                <td>${crearSwitch(p.idPermiso, 'bitAgregar', p.bitAgregar)}</td>
                <td>${crearSwitch(p.idPermiso, 'bitEditar', p.bitEditar)}</td>
                <td>${crearSwitch(p.idPermiso, 'bitEliminar', p.bitEliminar)}</td>
                <td>${crearSwitch(p.idPermiso, 'bitDetalle', p.bitDetalle)}</td>
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

async function guardarMatriz() {
    const idPerfil = document.getElementById('selectPerfil').value;
    const mensaje = document.getElementById('mensajeMatriz');
    const boton = document.getElementById('btnGuardarMatriz');

    if (!idPerfil) {
        alert('Seleccione un perfil antes de guardar la matriz.');
        return;
    }

    boton.disabled = true;
    boton.textContent = 'Guardando...';

    const filas = Array.from(document.querySelectorAll('#tablaPermisosBody tr'));
    const permisosAEnviar = filas.map(fila => {
        const idModulo = Number(fila.dataset.idmodulo || 0);
        const chev = (name) => fila.querySelector(`input[onchange*="${name}"]`)?.checked ? 1 : 0;

        return {
            idModulo,
            bitConsulta: chev('bitConsulta'),
            bitAgregar: chev('bitAgregar'),
            bitEditar: chev('bitEditar'),
            bitEliminar: chev('bitEliminar'),
            bitDetalle: chev('bitDetalle')
        };
    }).filter(item => item.idModulo > 0);

    try {
        const resp = await fetch('/permisosperfil/guardar-matriz', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            body: JSON.stringify({ idPerfil: Number(idPerfil), permisos: permisosAEnviar })
        });

        const result = await resp.json();

        if (!resp.ok) throw new Error(result.message || 'Error al guardar matriz');

        mensaje.textContent = 'Matriz guardada correctamente.';
        mensaje.className = 'text-success';
        cargarPermisosTabla();
    } catch (err) {
        mensaje.textContent = 'Error guardando matriz: ' + err.message;
        mensaje.className = 'text-danger';
        console.error(err);
    } finally {
        boton.disabled = false;
        boton.textContent = 'Guardar Matriz de Permisos';
    }
}
</script>