<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;

class AuthController extends BaseController
{
    protected $userModel;
    protected $jwtSecret;
    protected $jwtExpireDays;

    public function __construct()
    {
        $this->userModel     = new UserModel();
        $this->jwtSecret     = env('JWT_SECRET_KEY', '');
        $this->jwtExpireDays = (int) env('JWT_EXPIRE_DAYS', 5);
    }

    /**
     * POST /api/auth/login
     */
    public function login()
    {
        if (empty($this->jwtSecret)) {
            return $this->respond([
                'status'  => false,
                'message' => 'JWT secret key is not configured',
            ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        $requestData = $this->request->getJSON(true) ?: $this->request->getPost();

        $email    = $requestData['email'] ?? null;
        $password = $requestData['password'] ?? null;

        // Validasi input sederhana
        if (! $email || ! $password) {
            return $this->respond([
                'status'  => false,
                'message' => 'Email dan password wajib diisi',
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $user = $this->userModel
            ->where('email', $email)
            ->first();

        if (! $user) {
            return $this->respond([
                'status'  => false,
                'message' => 'Email atau password salah',
            ], ResponseInterface::HTTP_UNAUTHORIZED);
        }

        if (! password_verify($password, $user['password_hash'])) {
            return $this->respond([
                'status'  => false,
                'message' => 'Email atau password salah',
            ], ResponseInterface::HTTP_UNAUTHORIZED);
        }

        $issuedAt   = time();
        $expireTime = $issuedAt + ($this->jwtExpireDays * 24 * 60 * 60); // 5 hari

        $payload = [
            'sub'     => $user['id'],
            'user_id' => $user['id'],
            'name'    => $user['name'],
            'email'   => $user['email'],
            'role'    => $user['role'],
            'iat'     => $issuedAt,
            'exp'     => $expireTime,
        ];

        $token = JWT::encode($payload, $this->jwtSecret, 'HS256');

        return $this->respond([
            'status'  => true,
            'message' => 'Login berhasil',
            'data'    => [
                'token'       => $token,
                'token_type'  => 'Bearer',
                'expires_in'  => $expireTime - $issuedAt,
                'user'        => [
                    'id'    => $user['id'],
                    'name'  => $user['name'],
                    'email' => $user['email'],
                    'role'  => $user['role'],
                ],
            ],
        ], ResponseInterface::HTTP_OK);
    }

    /**
     * GET /api/auth/me
     * Perlu filter JWT, payload token di-set di JwtFilter sebagai $request->userData
     */
    public function me()
    {
        $userData = $this->request->userData ?? null;

        if (! $userData) {
            return $this->respond([
                'status'  => false,
                'message' => 'User data not found in token',
            ], ResponseInterface::HTTP_UNAUTHORIZED);
        }

        return $this->respond([
            'status' => true,
            'data'   => [
                'id'    => $userData->user_id ?? null,
                'name'  => $userData->name ?? null,
                'email' => $userData->email ?? null,
                'role'  => $userData->role ?? null,
            ],
        ], ResponseInterface::HTTP_OK);
    }

    protected function respond($body = null, int $statusCode = 200)
    {
        return service('response')
            ->setStatusCode($statusCode)
            ->setJSON($body);
    }
}