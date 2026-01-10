<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\JwtAuth;
use Config\Services;

class JwtAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $jwt = new JwtAuth();
        $token = $jwt->getBearerToken();

        // Check if token exists
        if (!$token) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON([
                    'status' => false,
                    'message' => 'Token not provided',
                    'error' => 'Authorization header required'
                ]);
        }

        // Validate token
        $userData = $jwt->validateToken($token);
        if (!$userData) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON([
                    'status' => false,
                    'message' => 'Invalid or expired token',
                    'error' => 'Token validation failed'
                ]);
        }

        // Store user data in request for later use
        $request->user = $userData->data;

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
