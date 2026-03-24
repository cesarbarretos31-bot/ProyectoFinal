<?php

namespace App\Controllers;

use App\Models\PerfilModel;
use CodeIgniter\API\ResponseTrait;

class Perfil extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $model = new PerfilModel();
        // Paginado para 5 filas según el PDF 
        $data = $model->paginate(5);
        return $this->respond([
            'perfiles' => $data,
            'pager'    => $model->pager->links()
        ]);
    }

    public function crear()
    {
        $model = new PerfilModel();
        $data = [
            'strNombrePerfil'  => $this->request->getPost('strNombrePerfil'), // 
            'bitAdministrador' => $this->request->getPost('bitAdministrador') ? 1 : 0 // 
        ];

        if ($model->insert($data)) {
            return $this->respondCreated(['msg' => 'Perfil creado']);
        }
        return $this->fail('Error al guardar');
    }

    public function eliminar($id)
    {
        $model = new PerfilModel();
        if ($model->delete($id)) {
            return $this->respondDeleted(['msg' => 'Perfil eliminado']);
        }
        return $this->fail('No se pudo eliminar');
    }
}