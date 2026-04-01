<?php
namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\API\ResponseTrait;

class Usuarios extends BaseController {
    use ResponseTrait;

    public function vista()
    {
        $permisos = $this->getPermisosModulo('Usuario');
        return view('modulos/usuario_view', ['permisos' => $permisos]);
    }

    public function listar()
    {
        $model = new UsuarioModel();

        $page = (int) $this->request->getGet('page') ?: 1;
        $search = trim($this->request->getGet('search'));

        $model->select('Usuario.*, Perfil.strNombrePerfil as perfil')
            ->join('Perfil', 'Perfil.id = Usuario.idPerfil', 'left');

        if ($search !== '') {
            $model->groupStart()
                ->like('Usuario.strNombreUsuario', $search)
                ->orLike('Usuario.strCorreo', $search)
                ->orLike('Perfil.strNombrePerfil', $search)
            ->groupEnd();
        }

        $usuarios = $model->orderBy('Usuario.id', 'DESC')->paginate(5, 'default');

        return $this->respond([
            'data' => $usuarios,
            'pager' => [
                'total' => (int) $model->pager->getPageCount(),
                'current' => $page,
                'totalRows' => (int) $model->pager->getTotal(),
            ],
        ]);
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
            'strNombreUsuario' => 'required|alpha_numeric_space|min_length[3]|max_length[100]',
            'idPerfil' => 'required|integer|greater_than[0]',
            'idEstado' => 'required|in_list[0,1]',
            'strCorreo' => 'required|valid_email|max_length[150]',
            'strNumeroCelular' => 'permit_empty|numeric|min_length[10]|max_length[15]',
            'strPwd' => ($id ? 'permit_empty|min_length[6]|max_length[80]' : 'required|min_length[6]|max_length[80]')
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