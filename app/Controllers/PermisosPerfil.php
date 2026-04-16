<?php
namespace App\Controllers;

use App\Models\PermisosModel;
use CodeIgniter\API\ResponseTrait;

class PermisosPerfil extends BaseController {
    use ResponseTrait;

    public function vista() {
        $permisos = $this->getPermisosModulo('Permisos Perfil');
        return view('modulos/permisosperfil_view', ['permisos' => $permisos]);
    }

    public function mostrar($idPerfil) {
        $db = \Config\Database::connect();
        
        // Obtener info del perfil para validar si es administrador
        $perfil = $db->table('Perfil')->where('id', $idPerfil)->get()->getRow();
        
        $builder = $db->table('Modulo m')
            ->select('m.id as idModulo, m.strNombreModulo, IFNULL(p.id, 0) as idPermiso, IFNULL(p.bitConsulta, 0) as bitConsulta, IFNULL(p.bitAgregar, 0) as bitAgregar, IFNULL(p.bitEditar, 0) as bitEditar, IFNULL(p.bitEliminar, 0) as bitEliminar, IFNULL(p.bitDetalle, 0) as bitDetalle')
            ->join('PermisosPerfil p', 'p.idModulo = m.id AND p.idPerfil = '.$idPerfil, 'left')
            ->orderBy('m.id', 'ASC');

        $data = $builder->get()->getResultArray();
        
        // Si es Administrador, mostrar TODOS los permisos en 1
        if ($perfil && intval($perfil->bitAdministrador) === 1) {
            foreach ($data as &$modulo) {
                $modulo['bitConsulta'] = 1;
                $modulo['bitAgregar'] = 1;
                $modulo['bitEditar'] = 1;
                $modulo['bitEliminar'] = 1;
                $modulo['bitDetalle'] = 1;
            }
        }
        
        return $this->respond($data);
    }

    public function listar() {
        $model = new PermisosModel();
        return $this->respond($model->orderBy('id', 'ASC')->findAll());
    }

    public function actualizar() {
        $model = new PermisosModel();
        $id = $this->request->getPost('id');
        $campo = $this->request->getPost('campo');
        $valor = $this->request->getPost('valor');

        $data = [$campo => $valor];

        if ($model->update($id, $data)) {
            return $this->respond(['msg' => 'Permiso actualizado']);
        }
        return $this->fail('Error al actualizar');
    }

    public function guardar() {
        $model = new PermisosModel();
        $data = [
            'idModulo' => $this->request->getPost('idModulo'),
            'idPerfil' => $this->request->getPost('idPerfil'),
            'bitAgregar' => $this->request->getPost('bitAgregar') ? 1 : 0,
            'bitEditar' => $this->request->getPost('bitEditar') ? 1 : 0,
            'bitConsulta' => $this->request->getPost('bitConsulta') ? 1 : 0,
            'bitEliminar' => $this->request->getPost('bitEliminar') ? 1 : 0,
            'bitDetalle' => $this->request->getPost('bitDetalle') ? 1 : 0,
        ];

        $insert = $model->insert($data);
        if ($insert) {
            return $this->respondCreated(['id' => $insert, 'msg' => 'Permiso creado']);
        }
        return $this->fail('Error al guardar permiso');
    }

    public function guardarMatriz() {
        $model = new PermisosModel();
        $input = $this->request->getJSON(true);

        if (!$input || empty($input['idPerfil']) || empty($input['permisos'])) {
            return $this->failValidationErrors('Datos incompletos');
        }

        $idPerfil = (int) $input['idPerfil'];
        if ($idPerfil <= 0) {
            return $this->failValidationErrors('Perfil inválido');
        }

        // VALIDACIÓN IMPORTANTE: No permitir editar permisos de Administrador
        // Los administradores tienen TODOS los permisos automáticamente
        $db = \Config\Database::connect();
        $perfil = $db->table('Perfil')->where('id', $idPerfil)->get()->getRow();
        
        if ($perfil && intval($perfil->bitAdministrador) === 1) {
            return $this->fail('No se pueden editar permisos de un perfil Administrador. '
                             . 'Los administradores tienen automáticamente TODOS los permisos (Consultar, Agregar, Editar, Eliminar, Detalle).',
                             403);
        }

        $permisos = $input['permisos'];
        $db->transStart();

        foreach ($permisos as $fila) {
            if (empty($fila['idModulo'])) continue;

            $data = [
                'idPerfil' => $idPerfil,
                'idModulo' => (int) $fila['idModulo'],
                'bitConsulta' => isset($fila['bitConsulta']) ? (int)$fila['bitConsulta'] : 0,
                'bitAgregar' => isset($fila['bitAgregar']) ? (int)$fila['bitAgregar'] : 0,
                'bitEditar' => isset($fila['bitEditar']) ? (int)$fila['bitEditar'] : 0,
                'bitEliminar' => isset($fila['bitEliminar']) ? (int)$fila['bitEliminar'] : 0,
                'bitDetalle' => isset($fila['bitDetalle']) ? (int)$fila['bitDetalle'] : 0,
            ];

            $exist = $model->where('idPerfil', $idPerfil)
                          ->where('idModulo', $data['idModulo'])
                          ->first();

            if ($exist) {
                $model->update($exist['id'] ?? $exist['id'], $data);
            } else {
                $model->insert($data);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->failServerError('Error guardando la matriz de permisos');
        }

        return $this->respond(['msg' => 'Matriz de permisos guardada correctamente']);
    }

    public function eliminar($id = null) {
        $model = new PermisosModel();
        if (!$model->delete($id)) {
            return $this->fail('Error al eliminar permiso');
        }
        return $this->respondDeleted(['msg' => 'Permiso eliminado']);
    }
}