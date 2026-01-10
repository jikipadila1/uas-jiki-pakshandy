<?php

namespace App\Controllers\API;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ObjekMelintasModel;
use App\Libraries\JwtAuth;

class ObjekMelintas extends ResourceController
{
    protected $format = 'json';
    protected $objekMelintasModel;
    protected $jwt;

    public function __construct()
    {
        $this->objekMelintasModel = new ObjekMelintasModel();
        $this->jwt = new JwtAuth();
    }

    /**
     * Get list of objek melintas with pagination
     * GET /api/objek-melintas
     *
     * @query int page Page number (default: 1)
     * @query int per_page Items per page (default: 10)
     * @query string search Search keyword
     * @return JSON Response with list of objek melintas
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
        $perPage = $this->request->getVar('per_page') ?? 10;
        $search = $this->request->getVar('search') ?? '';

        // Validate parameters
        if ($perPage > 100) {
            $perPage = 100;
        }

        // Get data
        $result = $this->objekMelintasModel->getObjekMelintasList($page, $perPage, $search);

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'message' => 'Data retrieved successfully',
            'data' => $result['data'],
            'pagination' => $result['pagination']
        ]);
    }

    /**
     * Get objek melintas by ID
     * GET /api/objek-melintas/{id}
     *
     * @param int $id Objek Melintas ID
     * @return JSON Response with objek melintas detail
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

        // Get objek melintas detail
        $objekMelintas = $this->objekMelintasModel->getObjekMelintasDetail($id);

        if (!$objekMelintas) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'Data not found',
                'error' => 'Objek Melintas with ID ' . $id . ' not found'
            ]);
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'message' => 'Data retrieved successfully',
            'data' => $objekMelintas
        ]);
    }

    /**
     * Create new objek melintas
     * POST /api/objek-melintas
     *
     * @return JSON Response with created objek melintas
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
        if (!isset($json['tipe_objek']) || !isset($json['lokasi']) || !isset($json['tanggal']) || !isset($json['jumlah'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'Missing required fields',
                'error' => 'tipe_objek, lokasi, tanggal, and jumlah are required'
            ]);
        }

        // Prepare data
        $data = [
            'tipe_objek' => $json['tipe_objek'],
            'jumlah' => $json['jumlah'],
            'lokasi' => $json['lokasi'],
            'tanggal' => $json['tanggal'],
            'deskripsi' => $json['deskripsi'] ?? null,
            'created_by' => $userData->id
        ];

        // Validate data
        if (!$this->objekMelintasModel->save($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->objekMelintasModel->errors()
            ]);
        }

        // Get inserted data
        $insertedId = $this->objekMelintasModel->getInsertID();
        $objekMelintas = $this->objekMelintasModel->getObjekMelintasDetail($insertedId);

        return $this->response->setStatusCode(201)->setJSON([
            'status' => true,
            'message' => 'Data objek melintas created successfully',
            'data' => $objekMelintas
        ]);
    }

    /**
     * Update objek melintas
     * PUT /api/objek-melintas/{id}
     *
     * @param int $id Objek Melintas ID
     * @return JSON Response with updated objek melintas
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

        // Check if objek melintas exists
        $existing = $this->objekMelintasModel->find($id);
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'Data not found',
                'error' => 'Objek Melintas with ID ' . $id . ' not found'
            ]);
        }

        // Get JSON input
        $json = $this->request->getJSON(true);

        // Prepare data
        $data = [
            'id' => $id,
            'tipe_objek' => $json['tipe_objek'] ?? $existing['tipe_objek'],
            'jumlah' => $json['jumlah'] ?? $existing['jumlah'],
            'lokasi' => $json['lokasi'] ?? $existing['lokasi'],
            'tanggal' => $json['tanggal'] ?? $existing['tanggal'],
            'deskripsi' => $json['deskripsi'] ?? $existing['deskripsi']
        ];

        // Validate and update
        if (!$this->objekMelintasModel->save($data)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->objekMelintasModel->errors()
            ]);
        }

        // Get updated data
        $objekMelintas = $this->objekMelintasModel->getObjekMelintasDetail($id);

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'message' => 'Data objek melintas updated successfully',
            'data' => $objekMelintas
        ]);
    }

    /**
     * Delete objek melintas
     * DELETE /api/objek-melintas/{id}
     *
     * @param int $id Objek Melintas ID
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

        // Check if objek melintas exists
        $existing = $this->objekMelintasModel->find($id);
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'Data not found',
                'error' => 'Objek Melintas with ID ' . $id . ' not found'
            ]);
        }

        // Delete
        $this->objekMelintasModel->delete($id);

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'message' => 'Data objek melintas deleted successfully'
        ]);
    }
}
