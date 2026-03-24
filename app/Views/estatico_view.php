<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Menú Principal</a></li>
    <li class="breadcrumb-item active" id="breadcrumbModulo"><?= $titulo ?></li>
  </ol>
</nav>

<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h5 class="mb-0">Módulo Estático: <?= $titulo ?></h5>
    </div>
    <div class="card-body text-center p-5">
        <p class="text-muted">Este es un módulo estático según el requerimiento del proyecto.</p>
        
        <div class="d-flex justify-content-center gap-2">
            <button class="btn btn-success"><i class="fas fa-plus"></i> Crear</button>
            <button class="btn btn-warning"><i class="fas fa-edit"></i> Editar</button>
            <button class="btn btn-info"><i class="fas fa-search"></i> Consultar</button>
            <button class="btn btn-danger"><i class="fas fa-trash"></i> Eliminar</button>
            <button class="btn btn-secondary"><i class="fas fa-eye"></i> Detalle</button>
        </div>
        
        <hr>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Ejemplo ID</th>
                    <th>Dato Estático</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Dato de prueba 1</td>
                    <td><span class="badge bg-secondary">Sin función</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>