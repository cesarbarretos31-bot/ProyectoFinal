<?php
namespace App\Controllers;

use App\Models\PermisosModel;
use CodeIgniter\API\ResponseTrait;

class PermisosPerfil extends BaseController {
    use ResponseTrait;

    public function vista() {
        return view('modulos/permisosperfil_view');
    }

    public function mostrar($idPerfil) {
        $model = new PermisosModel();
        $data = $model->select('permisos_perfil.*, modulo.strNombreModulo')
                      ->join('modulo', 'modulo.id = permisos_perfil.idModulo')
                      ->where('idPerfil', $idPerfil)
                      ->findAll();
        return $this->respond($data);
    }

    public function listar() {
        $model = new PermisosModel();
        return $this->respond($model->orderBy('id', 'ASC')->findAll());
    }

    public function actualizar() {
        $model = new PermisosModel();
        $id = $this->request->getPost('id');
        $campo = $this->request->getPost('campo');
        $valor = $this->request->getPost('valor');

        $data = [$campo => $valor];

        if ($model->update($id, $data)) {
            return $this->respond(['msg' => 'Permiso actualizado']);
        }
        return $this->fail('Error al actualizar');
    }

    public function guardar() {
        $model = new PermisosModel();
        $data = [
            'idModulo' => $this->request->getPost('idModulo'),
            'idPerfil' => $this->request->getPost('idPerfil'),
            'bitAgregar' => $this->request->getPost('bitAgregar') ? 1 : 0,
            'bitEditar' => $this->request->getPost('bitEditar') ? 1 : 0,
            'bitConsulta' => $this->request->getPost('bitConsulta') ? 1 : 0,
            'bitEliminar' => $this->request->getPost('bitEliminar') ? 1 : 0,
            'bitDetalle' => $this->request->getPost('bitDetalle') ? 1 : 0,
        ];

        if ($model->insert($data)) {
            return $this->respondCreated(['msg' => 'Permiso creado']);
        }
        return $this->fail('Error al guardar permiso');
    }

    public function eliminar($id = null) {
        $model = new PermisosModel();
        if (!$model->delete($id)) {
            return $this->fail('Error al eliminar permiso');
        }
        return $this->respondDeleted(['msg' => 'Permiso eliminado']);
    }
}