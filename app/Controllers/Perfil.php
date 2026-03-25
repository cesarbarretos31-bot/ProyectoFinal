<?php

namespace App\Controllers;

use App\Models\PerfilModel;
use CodeIgniter\API\ResponseTrait;

class Perfil extends BaseController
{
    use ResponseTrait;

    public function index()
{
    $model = new \App\Models\PerfilModel();
    
    // Esto desactiva el Debug Toolbar solo para esta respuesta JSON
    if (ENVIRONMENT !== 'production') {
        service('toolbar')->respond();
    }

    $data = [
        'perfiles' => $model->paginate(5),
        'pager'    => $model->pager->links()
    ];

    return $this->response->setJSON($data); 
    // Usar setJSON es mejor que respond() para evitar que se cuele basura
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
    public function vista() {
    // Esta función solo devuelve el pedazo de HTML del módulo
    return view('modulos/perfil_view'); 
}
}