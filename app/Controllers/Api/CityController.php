<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CityModel;
use CodeIgniter\HTTP\ResponseInterface;

class CityController extends BaseController
{
    protected $cityModel;

    public function __construct()
    {
        $this->cityModel = new CityModel();
    }

    /**
     * GET /api/cities
     * List cities + pagination + search
     * Query: ?page=1&per_page=10&search=jakarta
     */
    public function index()
    {
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        $search  = trim((string) ($this->request->getGet('search') ?? ''));

        // Batas aman per_page
        if ($perPage < 1) {
            $perPage = 10;
        } elseif ($perPage > 100) {
            $perPage = 100;
        }

        if ($page < 1) {
            $page = 1;
        }

        $offset  = ($page - 1) * $perPage;

        // Builder manual supaya bisa hitung total + pagination + search
        $builder = $this->cityModel->builder();

        if ($search !== '') {
            $builder->like('nama_kota', $search);
        }

        // Clone query untuk hitung total
        $totalBuilder = clone $builder;
        $total        = $totalBuilder->countAllResults(false);

        // Ambil data page tertentu
        $cities = $builder
            ->orderBy('id', 'ASC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 0;

        return $this->respondJson([
            'status' => true,
            'data'   => $cities,
            'meta'   => [
                'page'        => $page,
                'per_page'    => $perPage,
                'total'       => $total,
                'total_pages' => $totalPages,
                'search'      => $search !== '' ? $search : null,
            ],
        ]);
    }

    /**
     * POST /api/cities
     * Body JSON: { "nama_kota": "Jakarta Selatan" }
     */
    public function store()
    {
        $input = $this->request->getJSON(true) ?: $this->request->getPost();

        if (! $this->cityModel->insert($input)) {
            return $this->respondJson([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $this->cityModel->errors(),
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        return $this->respondJson([
            'status'  => true,
            'message' => 'Kota berhasil ditambahkan',
            'data'    => [
                'id' => $this->cityModel->getInsertID(),
            ],
        ], ResponseInterface::HTTP_CREATED);
    }

    /**
     * PUT /api/cities/{id}
     * Body JSON: { "nama_kota": "Bandung Barat" }
     */
    public function update($id = null)
    {
        $id = (int) $id;

        if ($id <= 0) {
            return $this->respondJson([
                'status'  => false,
                'message' => 'ID kota tidak valid',
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $city = $this->cityModel->find($id);

        if (! $city) {
            return $this->respondJson([
                'status'  => false,
                'message' => 'Kota tidak ditemukan',
            ], ResponseInterface::HTTP_NOT_FOUND);
        }

        $input = $this->request->getJSON(true) ?: $this->request->getRawInput();

        if (! $this->cityModel->update($id, $input)) {
            return $this->respondJson([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $this->cityModel->errors(),
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        return $this->respondJson([
            'status'  => true,
            'message' => 'Kota berhasil diperbarui',
        ]);
    }

    /**
     * DELETE /api/cities/{id}
     */
    public function delete($id = null)
    {
        $id = (int) $id;

        if ($id <= 0) {
            return $this->respondJson([
                'status'  => false,
                'message' => 'ID kota tidak valid',
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $city = $this->cityModel->find($id);

        if (! $city) {
            return $this->respondJson([
                'status'  => false,
                'message' => 'Kota tidak ditemukan',
            ], ResponseInterface::HTTP_NOT_FOUND);
        }

        $this->cityModel->delete($id);

        return $this->respondJson([
            'status'  => true,
            'message' => 'Kota berhasil dihapus',
        ]);
    }

    // Helper kecil biar konsisten
    protected function respondJson($body, int $statusCode = 200)
    {
        return service('response')
            ->setStatusCode($statusCode)
            ->setJSON($body);
    }
}