<?php
namespace App\Controllers;
use App\Models\PerfilModel;
use CodeIgniter\API\ResponseTrait;

class Perfil extends BaseController {
    use ResponseTrait;

    public function vista() {
        return view('modulos/perfil_view');
    }

   public function index() {
    $model = new \App\Models\PerfilModel();
    // Requisito: Paginado para 5 filas 
    $data = [
        'perfiles' => $model->paginate(5), 
        'pager'    => $model->pager->links()
    ];
    // Asegura que no se envíe nada más que el JSON 
    return $this->response->setJSON($data);
}
    public function crear() {
        $model = new PerfilModel();
        $data = [
            'strNombrePerfil'  => $this->request->getPost('strNombrePerfil'),
            'bitAdministrador' => $this->request->getPost('bitAdministrador') ? 1 : 0
        ];
        if ($model->insert($data)) return $this->respondCreated(['status' => 'ok']);
        return $this->fail('Error al guardar');
    }

    public function eliminar($id) {
        $model = new PerfilModel();
        if ($model->delete($id)) return $this->respondDeleted(['status' => 'ok']);
        return $this->fail('Error al eliminar');
    }
}