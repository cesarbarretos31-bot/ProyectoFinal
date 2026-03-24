<?php
namespace App\Controllers;
use App\Models\UsuarioModel;
use CodeIgniter\API\ResponseTrait;

class Usuarios extends BaseController {
    use ResponseTrait;

    public function crear() {
        $model = new UsuarioModel();
        
        // Manejo de la imagen [cite: 38]
        $file = $this->request->getFile('foto');
        $nombreImagen = 'default.png';

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $nombreImagen = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads', $nombreImagen);
        }

        $data = [
            'strNombreUsuario' => $this->request->getPost('strNombreUsuario'),
            'idPerfil'         => $this->request->getPost('idPerfil'),
            'strPwd'           => password_hash($this->request->getPost('strPwd'), PASSWORD_BCRYPT),
            'idEstado'         => $this->request->getPost('idEstado'), // 1: Activo, 0: Inactivo [cite: 21, 29]
            'strCorreo'        => $this->request->getPost('strCorreo'),
            'strNumeroCelular' => $this->request->getPost('strNumeroCelular'),
            'strImagen'        => $nombreImagen
        ];

        if ($model->insert($data)) {
            return $this->respondCreated(['msg' => 'Usuario creado con éxito']);
        }
        return $this->fail('Error al crear usuario');
    }
}