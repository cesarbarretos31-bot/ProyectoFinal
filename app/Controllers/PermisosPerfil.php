<?php
namespace App\Controllers;

use App\Models\PermisosModel;
use CodeIgniter\API\ResponseTrait;

class PermisosPerfil extends BaseController {
    use ResponseTrait;

    // Listar permisos de un perfil específico
    public function mostrar($idPerfil) {
        $model = new PermisosModel();
        // Traemos los módulos y sus permisos actuales para ese perfil
        $data = $model->select('permisos_perfil.*, modulo.strNombreModulo')
                      ->join('modulo', 'modulo.id = permisos_perfil.idModulo')
                      ->where('idPerfil', $idPerfil)
                      ->findAll();
        return $this->respond($data);
    }

    // Guardar o actualizar un permiso (Toggle de bits)
    public function actualizar() {
        $model = new PermisosModel();
        $id = $this->request->getPost('id');
        $campo = $this->request->getPost('campo'); // bitAgregar, bitEditar, etc.
        $valor = $this->request->getPost('valor'); // 1 o 0

        $data = [$campo => $valor];
        
        if ($model->update($id, $data)) {
            return $this->respond(['msg' => 'Permiso actualizado']);
        }
        return $this->fail('Error al actualizar');
    }
}