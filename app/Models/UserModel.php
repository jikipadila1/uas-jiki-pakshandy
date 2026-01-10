<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'username',
        'email',
        'password',
        'full_name'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
        'email' => 'required|valid_email|is_unique[users.email]',
        'password' => 'required|min_length[6]',
        'full_name' => 'required|min_length[3]|max_length[150]'
    ];
    protected $validationMessages   = [
        'username' => [
            'required' => 'Username is required',
            'is_unique' => 'Username already exists'
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Invalid email format',
            'is_unique' => 'Email already exists'
        ]
    ];
    protected $skipValidation       = false;

    /**
     * Login user
     *
     * @param string $identifier Username or email
     * @param string $password Plain password
     * @return array|false User data or false if failed
     */
    public function login($identifier, $password)
    {
        // Find user by username or email
        $user = $this->where('username', $identifier)
                    ->orWhere('email', $identifier)
                    ->first();

        if (!$user) {
            return false;
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            return false;
        }

        // Remove password from returned data
        unset($user['password']);

        return $user;
    }

    /**
     * Get user by ID
     *
     * @param int $id User ID
     * @return array|false User data or false if not found
     */
    public function getUserById($id)
    {
        $user = $this->find($id);
        if ($user) {
            unset($user['password']);
            return $user;
        }
        return false;
    }
}