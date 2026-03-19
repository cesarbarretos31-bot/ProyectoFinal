<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthFilter implements FilterInterface
{
    /**
     * Este método se ejecuta ANTES de llegar al controlador
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Por ahora, para que puedas probar el Dashboard sin que te rebote,
        // vamos a dejar que pase. Una vez que veas el Dashboard, activamos el JWT.
        return; 
    }

    /**
     * Este método se ejecuta DESPUÉS del controlador (obligatorio por la interfaz)
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No se necesita lógica aquí por ahora
    }
}