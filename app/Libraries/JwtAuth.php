<?php

namespace App\Libraries;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Exception;

class JwtAuth
{
    private $secretKey;
    private $algorithm = 'HS256';
    private $tokenExpiration = 172800; // 2 days in seconds (48 hours * 60 minutes * 60 seconds)

    public function __construct()
    {
        $this->secretKey = getenv('JWT_SECRET') ?: 'your-secret-key-change-this-in-production';
    }

    /**
     * Generate JWT Token
     *
     * @param array $userData User data to encode in token
     * @return string JWT Token
     */
    public function generateToken($userData)
    {
        $issuedAt = time();
        $expire = $issuedAt + $this->tokenExpiration;

        $payload = [
            'iat'  => $issuedAt,
            'exp'  => $expire,
            'data' => $userData
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * Validate JWT Token
     *
     * @param string $token JWT Token to validate
     * @return object|false Decoded token data or false if invalid
     */
    public function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return $decoded;
        } catch (ExpiredException $e) {
            // Token has expired
            return false;
        } catch (Exception $e) {
            // Invalid token
            return false;
        }
    }

    /**
     * Get user data from token
     *
     * @param string $token JWT Token
     * @return object|false User data or false if invalid
     */
    public function getUserData($token)
    {
        $decoded = $this->validateToken($token);
        if ($decoded && isset($decoded->data)) {
            return $decoded->data;
        }
        return false;
    }

    /**
     * Check if token is expired
     *
     * @param string $token JWT Token
     * @return bool True if expired, false otherwise
     */
    public function isExpired($token)
    {
        try {
            JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return false;
        } catch (ExpiredException $e) {
            return true;
        } catch (Exception $e) {
            return true;
        }
    }

    /**
     * Get token from Authorization header
     *
     * @return string|null Token or null if not found
     */
    public function getBearerToken()
    {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $matches = [];
            if (preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}
