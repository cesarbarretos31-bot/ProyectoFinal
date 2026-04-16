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

        // Crear nuevo perfil
        $newProfileId = $model->insert($data);
        if (!$newProfileId) {
            return $this->failServerError('No se pudo crear el perfil.');
        }

        // Si el perfil es Administrador, asignar automáticamente TODOS los permisos
        if ($bitAdministrador === 1) {
            $this->asignarPermisosAdministrador($newProfileId);
        }

        return $this->respondCreated(['status' => 'success', 'message' => 'Perfil creado correctamente']);
    }

    /**
     * Asigna automáticamente TODOS los permisos a un perfil administrador
     * 
     * Cuando se crea un nuevo perfil de Administrador, se le otorgan automáticamente:
     * - bitConsulta = 1 (puede consultar)
     * - bitAgregar = 1 (puede agregar)
     * - bitEditar = 1 (puede editar)
     * - bitEliminar = 1 (puede eliminar)
     * - bitDetalle = 1 (puede ver detalles)
     * 
     * Esto se hace para TODOS los módulos disponibles en el sistema
     */
    private function asignarPermisosAdministrador($idPerfil)
    {
        $db = \Config\Database::connect();
        
        // Obtener todos los módulos disponibles
        $modulos = $db->table('Modulo')->select('id')->get()->getResultArray();
        
        // Asignar todos los permisos para cada módulo
        foreach ($modulos as $modulo) {
            $db->table('PermisosPerfil')->insert([
                'idPerfil' => $idPerfil,
                'idModulo' => $modulo['id'],
                'bitConsulta' => 1,
                'bitAgregar' => 1,
                'bitEditar' => 1,
                'bitEliminar' => 1,
                'bitDetalle' => 1,
            ]);
        }
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