<?php

namespace App\Controllers;

use App\Models\PerfilModel;
use CodeIgniter\API\ResponseTrait;

class Perfil extends BaseController
{
    use ResponseTrait;

    public function vista()
    {
        $permisos = $this->getPermisosModulo('Perfil');
        return view('modulos/perfil_view', ['permisos' => $permisos]);
    }

    public function listar()
    {
        $model = new PerfilModel();
        $page = (int) $this->request->getGet('page') ?: 1;
        $search = trim($this->request->getGet('search'));

        if ($search !== '') {
            $model->like('strNombrePerfil', $search);
        }

        $perfiles = $model->orderBy('id', 'DESC')->paginate(5, 'default');

        return $this->respond([
            'data' => $perfiles,
            'pager' => [
                'total' => (int) $model->pager->getPageCount(),
                'current' => $page,
                'totalRows' => (int) $model->pager->getTotal(),
            ]
        ]);
    }

    public function guardar()
    {
        $model = new PerfilModel();

        $id = $this->request->getPost('id');
        $strNombrePerfil = trim($this->request->getPost('strNombrePerfil'));
        $bitAdmin = $this->request->getPost('bitAdministrador');
        
        // Asegurar que bitAdministrador sea 0 o 1
        $bitAdministrador = ($bitAdmin == '1' || $bitAdmin == 1) ? 1 : 0;
        
        // Validar nombre
        if (!$strNombrePerfil || strlen($strNombrePerfil) < 3 || strlen($strNombrePerfil) > 100) {
            return $this->failValidationErrors(['strNombrePerfil' => 'El nombre debe tener entre 3 y 100 caracteres']);
        }

        $data = [
            'strNombrePerfil' => $strNombrePerfil,
            'bitAdministrador' => $bitAdministrador,
        ];

        if ($id) {
            if (!$model->update($id, $data)) {
                return $this->failServerError('No se pudo actualizar el perfil.');
            }
            return $this->respond(['status' => 'success', 'message' => 'Perfil actualizado correctamente']);
        }

        if (!$model->insert($data)) {
            return $this->failServerError('No se pudo crear el perfil.');
        }

        return $this->respondCreated(['status' => 'success', 'message' => 'Perfil creado correctamente']);
    }

    public function eliminar($id = null)
    {
        $model = new PerfilModel();
        $perfil = $model->find($id);

        if (!$perfil) {
            return $this->failNotFound("Perfil con ID $id no encontrado");
        }

        if (!$model->delete($id)) {
            return $this->failServerError('No se pudo eliminar el perfil.');
        }

        return $this->respondDeleted(['status' => 'success', 'message' => 'Perfil eliminado correctamente']);
    }

    public function obtener($id = null)
    {
        $model = new PerfilModel();
        $perfil = $model->find($id);

        if (!$perfil) {
            return $this->failNotFound("Perfil con ID $id no encontrado");
        }

        return $this->respond($perfil);
    }

    public function crear()
    {
        $model = new PerfilModel();

        $data = [
            'strNombrePerfil' => trim($this->request->getPost('strNombrePerfil')),
            'bitAdministrador' => $this->request->getPost('bitAdministrador') ? 1 : 0,
        ];

        if (!$this->validate([
            'strNombrePerfil' => 'required|min_length[3]|max_length[100]',
        ])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $id = $model->insert($data);

        if (!$id) {
            return $this->failServerError('No se pudo crear el perfil.');
        }

        return $this->respondCreated(['id' => $id, 'message' => 'Perfil creado correctamente']);
    }

    public function actualizar($id = null)
    {
        $model = new PerfilModel();
        $perfil = $model->find($id);

        if (!$perfil) {
            return $this->failNotFound("Perfil con ID $id no encontrado");
        }

        $data = [
            'strNombrePerfil' => trim($this->request->getVar('strNombrePerfil')),
            'bitAdministrador' => $this->request->getVar('bitAdministrador') ? 1 : 0,
        ];

        if (!$this->validate([
            'strNombrePerfil' => 'required|alpha_numeric_space|min_length[3]|max_length[100]',
            'bitAdministrador' => 'required|in_list[0,1]'
        ])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $updated = $model->update($id, $data);

        if ($updated === false) {
            return $this->failServerError('No se pudo actualizar el perfil.');
        }

        return $this->respond(['message' => 'Perfil actualizado correctamente']);
    }
}