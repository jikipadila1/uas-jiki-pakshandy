<?php

namespace App\Controllers\API;

use CodeIgniter\RESTful\ResourceController;
use App\Models\PelanggaranModel;
use App\Libraries\JwtAuth;

class Pelanggaran extends ResourceController
{
    protected $format = 'json';
    protected $pelanggaranModel;
    protected $jwt;

    public function __construct()
    {
        $this->pelanggaranModel = new PelanggaranModel();
        $this->jwt = new JwtAuth();
    }

    /**
     * Get list of pelanggaran with pagination
     * GET /api/pelanggaran
     *
     * @query int page Page number (default: 1)
     * @query int per_page Items per page (default: 20)
     * @query string search Search keyword
     * @return JSON Response with list of pelanggaran
     */
    public function index()
    {
        // Get JWT token and validate
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

        // Get pagination parameters
        $page = $this->request->getVar('page') ?? 1;
        $perPage = $this->request->getVar('per_page') ?? 20;
        $search = $this->request->getVar('search') ?? '';

        // Validate parameters
        if ($perPage > 100) {
            $perPage = 100;
        }

        // Get data
        $result = $this->pelanggaranModel->getPelanggaranList($page, $perPage, $search);

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'message' => 'Data retrieved successfully',
            'data' => $result['data'],
            'pagination' => $result['pagination']
        ]);
    }

    /**
     * Get pelanggaran by ID
     * GET /api/pelanggaran/{id}
     *
     * @param int $id Pelanggaran ID
     * @return JSON Response with pelanggaran detail
     */
    public function show($id = null)
    {
        // Get JWT token and validate
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

        // Get pelanggaran detail
        $pelanggaran = $this->pelanggaranModel->getPelanggaranDetail($id);

        if (!$pelanggaran) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'Data not found',
                'error' => 'Pelanggaran with ID ' . $id . ' not found'
            ]);
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'message' => 'Data retrieved successfully',
            'data' => $pelanggaran
        ]);
    }

    /**
     * Create new pelanggaran
     * POST /api/pelanggaran
     *
     * @return JSON Response with created pelanggaran
     */
    public function create()
    {
        // Get JWT token and validate
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

        // Get JSON input
        $json = $this->request->getJSON(true);

        // Validate required fields
        if (!isset($json['tipe_pelanggaran']) || !isset($json['lokasi']) || !isset($json['tanggal'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'Missing required fields',
                'error' => 'tipe_pelanggaran, lokasi, and tanggal are required'
            ]);
        }

        // Prepare data
        $data = [
            'tipe_pelanggaran' => $json['tipe_pelanggaran'],
            'lokasi' => $json['lokasi'],
            'tanggal' => $json['tanggal'],
            'deskripsi' => $json['deskripsi'] ?? null,
            'foto' => $json['foto'] ?? null,
            'created_by' => $userData->id
        ];

        // Validate data
        if (!$this->pelanggaranModel->save($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->pelanggaranModel->errors()
            ]);
        }

        // Get inserted data
        $insertedId = $this->pelanggaranModel->getInsertID();
        $pelanggaran = $this->pelanggaranModel->getPelanggaranDetail($insertedId);

        return $this->response->setStatusCode(201)->setJSON([
            'status' => true,
            'message' => 'Data pelanggaran created successfully',
            'data' => $pelanggaran
        ]);
    }

    /**
     * Update pelanggaran
     * PUT /api/pelanggaran/{id}
     *
     * @param int $id Pelanggaran ID
     * @return JSON Response with updated pelanggaran
     */
    public function update($id = null)
    {
        // Get JWT token and validate
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

        // Check if pelanggaran exists
        $existing = $this->pelanggaranModel->find($id);
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'Data not found',
                'error' => 'Pelanggaran with ID ' . $id . ' not found'
            ]);
        }

        // Get JSON input
        $json = $this->request->getJSON(true);

        // Prepare data
        $data = [
            'id' => $id,
            'tipe_pelanggaran' => $json['tipe_pelanggaran'] ?? $existing['tipe_pelanggaran'],
            'lokasi' => $json['lokasi'] ?? $existing['lokasi'],
            'tanggal' => $json['tanggal'] ?? $existing['tanggal'],
            'deskripsi' => $json['deskripsi'] ?? $existing['deskripsi'],
            'foto' => $json['foto'] ?? $existing['foto']
        ];

        // Validate and update
        if (!$this->pelanggaranModel->save($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->pelanggaranModel->errors()
            ]);
        }

        // Get updated data
        $pelanggaran = $this->pelanggaranModel->getPelanggaranDetail($id);

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'message' => 'Data pelanggaran updated successfully',
            'data' => $pelanggaran
        ]);
    }

    /**
     * Delete pelanggaran
     * DELETE /api/pelanggaran/{id}
     *
     * @param int $id Pelanggaran ID
     * @return JSON Response
     */
    public function delete($id = null)
    {
        // Get JWT token and validate
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

        // Check if pelanggaran exists
        $existing = $this->pelanggaranModel->find($id);
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'Data not found',
                'error' => 'Pelanggaran with ID ' . $id . ' not found'
            ]);
        }

        // Delete
        $this->pelanggaranModel->delete($id);

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'message' => 'Data pelanggaran deleted successfully'
        ]);
    }
}
