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
        $db = \Config\Database::connect();
        $builder = $db->table('Modulo m')
            ->select('m.id as idModulo, m.strNombreModulo, IFNULL(p.id, 0) as idPermiso, IFNULL(p.bitConsulta, 0) as bitConsulta, IFNULL(p.bitAgregar, 0) as bitAgregar, IFNULL(p.bitEditar, 0) as bitEditar, IFNULL(p.bitEliminar, 0) as bitEliminar, IFNULL(p.bitDetalle, 0) as bitDetalle')
            ->join('PermisosPerfil p', 'p.idModulo = m.id AND p.idPerfil = '.$idPerfil, 'left')
            ->orderBy('m.id', 'ASC');

        $data = $builder->get()->getResultArray();
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

        $insert = $model->insert($data);
        if ($insert) {
            return $this->respondCreated(['id' => $insert, 'msg' => 'Permiso creado']);
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