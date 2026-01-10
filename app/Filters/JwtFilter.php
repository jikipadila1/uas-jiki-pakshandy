<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Exception;

class JwtFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (! $authHeader) {
            return $this->unauthorized('Authorization header is missing');
        }

        // Format yang diharapkan: "Bearer <token>"
        if (! preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $this->unauthorized('Invalid Authorization header format');
        }

        $token = $matches[1] ?? null;

        if (! $token) {
            return $this->unauthorized('Token is missing');
        }

        $secretKey = env('JWT_SECRET_KEY');

        if (! $secretKey) {
            return $this->unauthorized('JWT secret key is not configured');
        }

        try {
            // Decode & verify token
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            // Optional: bisa cek manual exp kalau mau
            // if (isset($decoded->exp) && $decoded->exp < time()) { ... }

            // Simpan payload token ke request supaya bisa diakses di controller
            // Di controller: $this->request->userData
            $request->userData = $decoded;
        } catch (ExpiredException $e) {
            return $this->unauthorized('Token has expired');
        } catch (Exception $e) {
            return $this->unauthorized('Invalid token: ' . $e->getMessage());
        }

        // Lanjut ke controller
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu diapa-apakan
    }

    private function unauthorized(string $message): ResponseInterface
    {
        $response = service('response');

        return $response
            ->setStatusCode(401)
            ->setJSON([
                'status'  => false,
                'message' => $message,
            ]);
    }
}