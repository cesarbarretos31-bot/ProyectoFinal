<?php
namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\API\ResponseTrait;

class Usuarios extends BaseController {
    use ResponseTrait;

    public function vista()
    {
        return view('modulos/usuario_view');
    }

    public function listar()
    {
        $model = new UsuarioModel();
        $usuarios = $model->select('Usuario.*, Perfil.strNombrePerfil as perfil')
            ->join('Perfil', 'Perfil.id = Usuario.idPerfil', 'left')
            ->orderBy('Usuario.id', 'DESC')
            ->findAll();

        return $this->respond($usuarios);
    }

    public function obtener($id = null)
    {
        $model = new UsuarioModel();
        $usuario = $model->find($id);
        if (!$usuario) {
            return $this->failNotFound('Usuario no encontrado');
        }
        return $this->respond($usuario);
    }

    public function guardar()
    {
        $model = new UsuarioModel();

        $id = $this->request->getPost('id');
        $data = [
            'strNombreUsuario' => trim($this->request->getPost('strNombreUsuario')),
            'idPerfil' => $this->request->getPost('idPerfil'),
            'idEstado' => $this->request->getPost('idEstado'),
            'strCorreo' => trim($this->request->getPost('strCorreo')),
            'strNumeroCelular' => trim($this->request->getPost('strNumeroCelular')),
        ];

        if ($this->request->getPost('strPwd')) {
            $data['strPwd'] = password_hash($this->request->getPost('strPwd'), PASSWORD_BCRYPT);
        }

        $file = $this->request->getFile('strImagen');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $filename = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads', $filename);
            $data['strImagen'] = $filename;
        }

        if (!$this->validate([ 
            'strNombreUsuario' => 'required|min_length[3]|max_length[100]'
        ])) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        if ($id) {
            if (!$model->update($id, $data)) {
                return $this->failServerError('No se pudo actualizar el usuario');
            }
            return $this->respond(['status' => 'success', 'message' => 'Usuario actualizado']);
        }

        if (!$model->insert($data)) {
            return $this->failServerError('No se pudo crear el usuario');
        }

        return $this->respondCreated(['status' => 'success', 'message' => 'Usuario creado']);
    }

    public function eliminar($id = null)
    {
        $model = new UsuarioModel();
        $usuario = $model->find($id);
        if (!$usuario) {
            return $this->failNotFound('Usuario no encontrado');
        }

        try {
            if (!$model->delete($id)) {
                return $this->failServerError('No se pudo eliminar el usuario.');
            }
        } catch (\Exception $e) {
            return $this->failServerError('No se pudo eliminar el usuario: ' . $e->getMessage());
        }

        return $this->respondDeleted(['status' => 'success', 'message' => 'Usuario eliminado']);
    }
}