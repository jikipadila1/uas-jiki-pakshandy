<?php

namespace App\Controllers\API;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use App\Libraries\JwtAuth;

class Auth extends ResourceController
{
    protected $format = 'json';
    protected $userModel;
    protected $jwt;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jwt = new JwtAuth();
    }

    /**
     * Login endpoint
     * POST /api/auth/login
     *
     * @return JSON Response with JWT token
     */
    public function login()
    {
        // Get JSON input
        $json = $this->request->getJSON(true);

        // Validate input
        if (!isset($json['username']) || !isset($json['password'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'Username and password are required',
                'error' => 'Missing required fields'
            ]);
        }

        $username = $json['username'];
        $password = $json['password'];

        // Attempt login
        $user = $this->userModel->login($username, $password);

        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'message' => 'Invalid credentials',
                'error' => 'Username or password is incorrect'
            ]);
        }

        // Generate JWT token
        $token = $this->jwt->generateToken([
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'full_name' => $user['full_name']
        ]);

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'user' => $user
            ]
        ]);
    }

    /**
     * Validate token endpoint
     * GET /api/auth/validate
     *
     * @return JSON Response with user data
     */
    public function validateToken()
    {
        $token = $this->jwt->getBearerToken();

        if (!$token) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'message' => 'Token not provided',
                'error' => 'Authorization header required'
            ]);
        }

        $userData = $this->jwt->getUserData($token);

        if (!$userData) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'message' => 'Invalid or expired token',
                'error' => 'Token validation failed'
            ]);
        }

        // Get full user data
        $user = $this->userModel->getUserById($userData->id);

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'message' => 'Token is valid',
            'data' => [
                'user' => $user
            ]
        ]);
    }

    /**
     * Get current user profile
     * GET /api/auth/me
     *
     * @return JSON Response with user profile
     */
    public function me()
    {
        $token = $this->jwt->getBearerToken();

        if (!$token) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'message' => 'Token not provided',
                'error' => 'Authorization header required'
            ]);
        }

        $userData = $this->jwt->getUserData($token);

        if (!$userData) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => false,
                'message' => 'Invalid or expired token',
                'error' => 'Token validation failed'
            ]);
        }

        // Get full user data
        $user = $this->userModel->getUserById($userData->id);

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'message' => 'User profile retrieved successfully',
            'data' => [
                'user' => $user
            ]
        ]);
    }
}
