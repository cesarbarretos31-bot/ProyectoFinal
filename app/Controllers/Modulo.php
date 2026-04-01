<?php

namespace App\Controllers;

use App\Models\ModuloModel;
use CodeIgniter\API\ResponseTrait;

class Modulo extends BaseController
{
    use ResponseTrait;

    public function vista()
    {
        $permisos = $this->getPermisosModulo('Módulo');
        return view('modulos/modulo_view', ['permisos' => $permisos]);
    }

    public function listar()
    {
        $model = new ModuloModel();
        $page = (int) $this->request->getGet('page') ?: 1;
        $search = trim($this->request->getGet('search'));

        if ($search !== '') {
            $model->like('strNombreModulo', $search);
        }

        $datos = $model->orderBy('id', 'ASC')->paginate(5, 'default');

        return $this->respond([
            'data' => $datos,
            'pager' => [
                'total' => (int) $model->pager->getPageCount(),
                'current' => $page,
                'totalRows' => (int) $model->pager->getTotal(),
            ]
        ]);
    }

    public function obtener($id = null)
    {
        $model = new ModuloModel();
        $fila = $model->find($id);
        if (!$fila) {
            return $this->failNotFound('Módulo no encontrado');
        }
        return $this->respond($fila);
    }

    public function guardar()
    {
        $model = new ModuloModel();
        $id = $this->request->getPost('id');
        $data = ['strNombreModulo' => trim($this->request->getPost('strNombreModulo'))];

        if (!$this->validate(['strNombreModulo' => 'required|alpha_numeric_space|min_length[2]|max_length[100]'])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        if ($id) {
            $row = $model->find($id);
            if (!$row) {
                return $this->failNotFound('Módulo no encontrado');
            }
            if (!$model->update($id, $data)) {
                return $this->failServerError('No se pudo actualizar el módulo');
            }
            return $this->respond(['id' => $id, 'message' => 'Módulo actualizado']);
        }

        $insertId = $model->insert($data);
        if (!$insertId) return $this->failServerError('No se pudo crear el módulo');

        return $this->respondCreated(['id' => $insertId, 'message' => 'Módulo creado']);
    }

    public function actualizar($id = null)
    {
        $model = new ModuloModel();
        $fila = $model->find($id);
        if (!$fila) return $this->failNotFound('Módulo no encontrado');

        $data = ['strNombreModulo' => trim($this->request->getPost('strNombreModulo'))];

        if (!$this->validate(['strNombreModulo' => 'required|alpha_numeric_space|min_length[2]|max_length[100]'])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        if (!$model->update($id, $data)) {
            return $this->failServerError('No se pudo actualizar el módulo');
        }

        return $this->respond(['message' => 'Módulo actualizado']);
    }

    public function eliminar($id = null)
    {
        $model = new ModuloModel();
        $fila = $model->find($id);
        if (!$fila) return $this->failNotFound('Módulo no encontrado');

        try {
            if (!$model->delete($id)) {
                return $this->failServerError('No se pudo eliminar el módulo. Verifica relaciones.');
            }
        } catch (\Exception $e) {
            return $this->failServerError('No se pudo eliminar el módulo. Debe eliminar registros relacionados en Menu/PermisosPerfil primero. ' . $e->getMessage());
        }

        return $this->respondDeleted(['message' => 'Módulo eliminado']);
    }
}