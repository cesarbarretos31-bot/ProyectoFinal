<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    /**
     * Obtiene los permisos del usuario actual para un módulo específico
     * 
     * LÓGICA DE PERMISOS:
     * - bitConsulta: Permite ver/consultar registros del módulo
     * - bitAgregar: Permite crear nuevos registros
     * - bitEditar: Permite modificar registros existentes
     * - bitEliminar: Permite eliminar registros
     * - bitDetalle: Permite ver información detallada/completa de un registro (cuando está visible)
     * 
     * REGLA IMPORTANTE:
     * Si el perfil está marcado como Administrador (bitAdministrador = 1), 
     * se otorgan automáticamente TODOS los permisos sin necesidad de configurarlos en la matriz.
     * 
     * FLUJO:
     * 1. Verificar si el usuario tiene sesión válida
     * 2. Si es Administrador → retornar todos los permisos en 1
     * 3. Si NO es Administrador → consultar la matriz de permisos específica
     * 4. Si no hay permisos configurados → retornar todos los permisos en 0
     */
    protected function getPermisosModulo($nombreModulo)
    {
        $session = session();
        $idPerfil = $session->get('idPerfil');

        if (!$idPerfil) {
            return [
                'bitConsulta' => 0,
                'bitAgregar' => 0,
                'bitEditar' => 0,
                'bitEliminar' => 0,
                'bitDetalle' => 0
            ];
        }

        $db = \Config\Database::connect();
        
        // PRIMERO: Verificar si es administrador
        $perfil = $db->table('Perfil')->select('bitAdministrador')->where('id', $idPerfil)->get()->getRow();
        if ($perfil && intval($perfil->bitAdministrador) === 1) {
            // Administrador → todos los permisos automáticamente
            return [
                'bitConsulta' => 1,
                'bitAgregar' => 1,
                'bitEditar' => 1,
                'bitEliminar' => 1,
                'bitDetalle' => 1
            ];
        }

        // SEGUNDO: Si NO es administrador, consultar la matriz de permisos
        $sql = "SELECT
                    p.bitConsulta,
                    p.bitAgregar,
                    p.bitEditar,
                    p.bitEliminar,
                    p.bitDetalle
                FROM PermisosPerfil p
                JOIN Modulo m ON m.id = p.idModulo
                WHERE p.idPerfil = ? AND LOWER(TRIM(m.strNombreModulo)) = LOWER(TRIM(?))
                LIMIT 1";

        $query = $db->query($sql, [$idPerfil, $nombreModulo]);
        $permiso = $query->getRow();

        if ($permiso) {
            return [
                'bitConsulta' => (int) $permiso->bitConsulta,
                'bitAgregar' => (int) $permiso->bitAgregar,
                'bitEditar' => (int) $permiso->bitEditar,
                'bitEliminar' => (int) $permiso->bitEliminar,
                'bitDetalle' => (int) $permiso->bitDetalle
            ];
        }

        // TERCERO: Sin permisos configurados → denegar acceso
        return [
            'bitConsulta' => 0,
            'bitAgregar' => 0,
            'bitEditar' => 0,
            'bitEliminar' => 0,
            'bitDetalle' => 0
        ];
    }
}
