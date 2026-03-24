<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Seguridad</a></li>
    <li class="breadcrumb-item active">Usuario</li>
  </ol>
</nav>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between">
        <h5>Módulo Usuario</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalUsuario">Nuevo Usuario</button>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Imagen</th> <th>Usuario</th>
                    <th>Correo</th>
                    <th>Estado</th> <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaUsuarios"></tbody>
        </table>
        <div id="paginadoUsuarios"></div>
    </div>
</div>

<div class="modal fade" id="modalUsuario" tabindex="-1">
  <div class="modal-dialog">
    <form id="formUsuario" enctype="multipart/form-data"> <div class="modal-content">
            <div class="modal-body">
                <input type="text" name="strNombreUsuario" class="form-control mb-2" placeholder="Nombre de Usuario" required>
                <input type="email" name="strCorreo" class="form-control mb-2" placeholder="Correo" required>
                <input type="password" name="strPwd" class="form-control mb-2" placeholder="Contraseña" required>
                <select name="idPerfil" id="listPerfiles" class="form-select mb-2"></select>
                <input type="file" name="foto" class="form-control mb-2" accept="image/*"> </div>
            <div class="modal-footer">
                <button type="button" onclick="guardarUsuario()" class="btn btn-success">Guardar</button>
            </div>
        </div>
    </form>
  </div>
</div>

<script>
async function guardarUsuario() {
    const formData = new FormData(document.getElementById('formUsuario')); // [cite: 36]
    
    const response = await fetch('/usuarios/crear', {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` },
        body: formData
    });

    if (response.ok) {
        alert("Usuario creado ");
        location.reload();
    }
}
</script>