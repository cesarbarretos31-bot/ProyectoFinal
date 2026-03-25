<?php

namespace App\Controllers;

use App\Models\PerfilModel;
use CodeIgniter\API\ResponseTrait;

class Perfil extends BaseController
{
    use ResponseTrait;

    public function vista()
    {
        return view('modulos/perfil_view');
    }

    public function index()
    {
        $model = new PerfilModel();

        $data = [
            'perfiles' => $model->paginate(5),
            'pager'    => $model->pager->links()
        ];

        // LIMPIEZA EXTREMA: Detenemos el debug toolbar de CI4 para esta petición
        if (ENVIRONMENT !== 'production') {
            service('toolbar')->respond();
        }

        return $this->response->setJSON($data);
    }

    public function crear()
    {
        $model = new PerfilModel();
        $data = [
            'strNombrePerfil'  => $this->request->getPost('strNombrePerfil'),
            'bitAdministrador' => $this->request->getPost('bitAdministrador') ? 1 : 0
        ];

        if ($model->insert($data)) {
            return $this->respondCreated(['msg' => 'Perfil creado']);
        }
        return $this->fail('Error al guardar');
    }

    public function eliminar($id)
    {
        $model = new PerfilModel();
        try {
            if ($model->delete($id)) {
                return $this->respondDeleted(['msg' => 'Perfil eliminado']);
            }
        } catch (\Exception $e) {
            return $this->fail('No se pudo eliminar (posiblemente tiene usuarios asociados)');
        }
        return $this->fail('No se pudo eliminar');
    }
}