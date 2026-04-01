<div class="card p-4 shadow-sm border-0 fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark m-0">
            <i class="bi bi-shield-lock text-primary me-2"></i> Gestión de Perfiles
        </h3>
        <button class="btn btn-primary shadow-sm" onclick="mostrarFormularioPerfil()">
            <i class="bi bi-plus-lg"></i> Nuevo Perfil
        </button>
    </div>

    <div id="perfilMensaje" class="alert d-none" role="alert"></div>

    <div id="perfilFormulario" class="card p-4 mb-4 d-none">
        <h5>Registrar / Editar Perfil</h5>
        <form id="formPerfil" onsubmit="event.preventDefault(); guardarPerfil();">
            <input type="hidden" id="perfilId" value="" />
            <div class="mb-3">
                <label for="perfilNombre" class="form-label">Nombre del Perfil</label>
                <input type="text" id="perfilNombre" class="form-control" required minlength="3" maxlength="100" />
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" id="perfilAdministrador" class="form-check-input" />
                <label for="perfilAdministrador" class="form-check-label">Administrador</label>
            </div>
            <div>
                <button type="submit" class="btn btn-success">Guardar</button>
                <button type="button" class="btn btn-secondary" onclick="ocultarFormularioPerfil()">Cancelar</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-borderless table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Perfil</th>
                    <th>Administrador</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody id="perfilLista"></tbody>
        </table>
    </div>
</div>

<script>
    async function cargarPerfiles() {
        try {
            const resp = await fetch('<?= base_url('perfil/listar') ?>');
            const datos = await resp.json();
            const tbody = document.getElementById('perfilLista');
            tbody.innerHTML = '';

            if (!Array.isArray(datos) || datos.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay perfiles registrados.</td></tr>';
                return;
            }

            datos.forEach(perfil => {
                tbody.innerHTML += `
                    <tr>
                        <td>${perfil.id}</td>
                        <td>${perfil.strNombrePerfil}</td>
                        <td>${perfil.bitAdministrador ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>'}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-info me-1" onclick="editarPerfil(${perfil.id})">Editar</button>
                            <button class="btn btn-sm btn-danger" onclick="confirmarEliminarPerfil(${perfil.id})">Eliminar</button>
                        </td>
                    </tr>`;
            });
        } catch (err) {
            console.error(err);
            mostrarMensaje('Error al obtener perfiles', 'danger');
        }
    }

    function mostrarFormularioPerfil(perfil = null) {
        document.getElementById('perfilFormulario').classList.remove('d-none');
        document.getElementById('perfilMensaje').classList.add('d-none');

        if (perfil) {
            document.getElementById('perfilId').value = perfil.id;
            document.getElementById('perfilNombre').value = perfil.strNombrePerfil;
            document.getElementById('perfilAdministrador').checked = perfil.bitAdministrador == 1;
        } else {
            document.getElementById('perfilId').value = '';
            document.getElementById('perfilNombre').value = '';
            document.getElementById('perfilAdministrador').checked = false;
        }
    }

    function ocultarFormularioPerfil() {
        document.getElementById('perfilFormulario').classList.add('d-none');
    }

    async function guardarPerfil() {
        const id = document.getElementById('perfilId').value;
        const data = {
            strNombrePerfil: document.getElementById('perfilNombre').value.trim(),
            bitAdministrador: document.getElementById('perfilAdministrador').checked ? 1 : 0
        };

        try {
            let response;
            if (id) {
                response = await fetch(`<?= base_url('perfil') ?>/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
            } else {
                const formBody = new URLSearchParams(data);
                response = await fetch('<?= base_url('perfil') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formBody
                });
            }

            if (!response.ok) {
                const result = await response.json();
                throw new Error(result.message || 'Error en backend');
            }

            await cargarPerfiles();
            ocultarFormularioPerfil();
            mostrarMensaje('Perfil guardado correctamente.', 'success');

        } catch (err) {
            console.error(err);
            mostrarMensaje('No se pudo guardar el perfil. ' + err.message, 'danger');
        }
    }

    async function editarPerfil(id) {
        try {
            const resp = await fetch(`<?= base_url('perfil/obtener') ?>/${id}`);
            const perfil = await resp.json();

            if (!resp.ok) {
                throw new Error(perfil.message || 'Perfil no encontrado');
            }

            mostrarFormularioPerfil(perfil);
        } catch (err) {
            console.error(err);
            mostrarMensaje('Error al cargar perfil para edición.', 'danger');
        }
    }

    function confirmarEliminarPerfil(id) {
        if (confirm('¿Estás seguro de eliminar este perfil?')) {
            eliminarPerfil(id);
        }
    }

    async function eliminarPerfil(id) {
        try {
            const resp = await fetch(`<?= base_url('perfil') ?>/${id}`, { method: 'DELETE' });
            const data = await resp.json();

            if (!resp.ok) {
                throw new Error(data.message || 'Error al eliminar');
            }

            await cargarPerfiles();
            mostrarMensaje('Perfil eliminado correctamente.', 'success');
        } catch (err) {
            console.error(err);
            mostrarMensaje('No se pudo eliminar perfil. ' + err.message, 'danger');
        }
    }

    function mostrarMensaje(texto, tipo) {
        const alerta = document.getElementById('perfilMensaje');
        alerta.className = `alert alert-${tipo}`;
        alerta.textContent = texto;
        alerta.classList.remove('d-none');
    }

    document.addEventListener('DOMContentLoaded', cargarPerfiles);
</script>