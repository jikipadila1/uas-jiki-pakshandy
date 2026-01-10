<?php

namespace App\Models;

use CodeIgniter\Model;

class PelanggaranModel extends Model
{
    protected $table            = 'pelanggaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'tipe_pelanggaran',
        'lokasi',
        'tanggal',
        'deskripsi',
        'foto',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'tipe_pelanggaran' => 'required|in_list[contraflow,overspeed,traffic_jam]',
        'lokasi' => 'required|min_length[3]|max_length[255]',
        'tanggal' => 'required|valid_date'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    // Get pelanggaran with pagination
    public function getPelanggaranList($page = 1, $perPage = 20, $search = '')
    {
        $builder = $this->select('pelanggaran.*, users.full_name as created_by_name')
                       ->join('users', 'users.id = pelanggaran.created_by', 'left')
                       ->orderBy('pelanggaran.tanggal', 'DESC');

        // Search functionality
        if ($search) {
            $builder->groupStart()
                    ->like('pelanggaran.lokasi', $search)
                    ->orLike('pelanggaran.tipe_pelanggaran', $search)
                    ->orLike('pelanggaran.deskripsi', $search)
                    ->groupEnd();
        }

        $data = $builder->paginate($perPage, 'default', $page);

        // Get pager details
        $pager = $this->pager->getDetails();
        $total = $pager['total'] ?? 0;

        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $total > 0 ? ceil($total / $perPage) : 0
            ]
        ];
    }

    // Get pelanggaran by ID with user info
    public function getPelanggaranDetail($id)
    {
        return $this->select('pelanggaran.*, users.full_name as created_by_name')
                    ->join('users', 'users.id = pelanggaran.created_by', 'left')
                    ->where('pelanggaran.id', $id)
                    ->first();
    }
}
