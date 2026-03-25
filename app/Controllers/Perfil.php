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
    // Limpiar cualquier salida previa que pueda generar espacios en blanco
    if (ob_get_level() > 0) ob_end_clean();

    $model = new \App\Models\PerfilModel();
    $data = [
        'perfiles' => $model->paginate(5),
        'pager'    => $model->pager->links()
    ];

    // Configurar cabeceras manualmente para asegurar compatibilidad
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *'); // Evitar bloqueos de CORS si los hay
    
    echo json_encode($data);
    
    // IMPORTANTE: die() detiene a CodeIgniter antes de que inyecte el Toolbar de Debug
    die(); 
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