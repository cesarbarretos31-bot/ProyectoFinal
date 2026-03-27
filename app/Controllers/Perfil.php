<?php
// app/Controllers/Perfiles.php
namespace App\Controllers;
use App\Models\PerfilModel;

class Perfiles extends BaseController {
    
    public function vista() {
        return view('modulos/perfiles_view');
    }

    public function listar() {
        $model = new PerfilModel();
        $busqueda = $this->request->getVar('search');
        $pagina = $this->request->getVar('page') ?? 1;

        if (!empty($busqueda)) {
            $model->like('strNombrePerfil', $busqueda); // Filtro de búsqueda 
        }

        return $this->response->setJSON([
            'data' => $model->paginate(5, 'default', $pagina), // Paginado de 5 filas 
            'pager' => [
                'current' => (int)$pagina,
                'total' => $model->pager->getPageCount(),
                'totalRows' => $model->pager->getTotal()
            ]
        ]);
    }

    public function guardar() {
        $model = new PerfilModel();
        $id = $this->request->getVar('id');
        $data = [
            'strNombrePerfil'  => $this->request->getVar('strNombrePerfil'),
            'bitAdministrador' => $this->request->getVar('bitAdministrador') ? 1 : 0
        ];

        if ($id) {
            $model->update($id, $data); // Editar 
        } else {
            $model->insert($data); // Crear 
        }
        return $this->response->setJSON(['status' => 'success']);
    }

    public function eliminar($id) {
        $model = new PerfilModel();
        $model->delete($id); // Eliminar 
        return $this->response->setJSON(['status' => 'success']);
    }
}