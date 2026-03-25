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
    // 1. Limpiamos cualquier buffer de salida para evitar espacios en blanco
    if (ob_get_level() > 0) ob_end_clean();

    $model = new \App\Models\PerfilModel();
    $data = [
        'perfiles' => $model->paginate(5),
        'pager'    => $model->pager->links()
    ];

    // 2. Desactivamos el Toolbar de CodeIgniter por completo para esta respuesta
    if (ENVIRONMENT !== 'production') {
        service('toolbar')->respond();
    }

    // 3. Enviamos la respuesta y DETENEMOS la ejecución del script
    return $this->response->setJSON($data)->setStatusCode(200);
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