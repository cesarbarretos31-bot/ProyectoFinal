<?php

namespace App\Controllers;

use App\Models\PerfilModel;
use CodeIgniter\API\ResponseTrait;

class Perfil extends BaseController
{
    use ResponseTrait;

    public function vista() {
        return view('modulos/perfil_view');
    }

    public function index() {
        // Limpieza absoluta del buffer para evitar espacios o basura
        while (ob_get_level() > 0) ob_end_clean();

        $model = new PerfilModel();
        $data = [
            'perfiles' => $model->paginate(5),
            'pager'    => $model->pager->links()
        ];

        // Desactivar Toolbar manualmente por si Railway está en modo development
        if (ENVIRONMENT !== 'production') {
            service('toolbar')->respond();
        }

        return $this->response->setJSON($data);
    }

    public function crear() {
        $model = new PerfilModel();
        $data = [
            'strNombrePerfil'  => $this->request->getPost('strNombrePerfil'),
            'bitAdministrador' => $this->request->getPost('bitAdministrador') ? 1 : 0
        ];
        return ($model->insert($data)) ? $this->respondCreated(['msg' => 'ok']) : $this->fail('Error');
    }

    public function eliminar($id) {
        $model = new PerfilModel();
        return ($model->delete($id)) ? $this->respondDeleted(['msg' => 'ok']) : $this->fail('Error');
    }
}