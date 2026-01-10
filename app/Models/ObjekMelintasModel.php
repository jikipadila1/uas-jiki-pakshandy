<?php

namespace App\Models;

use CodeIgniter\Model;

class ObjekMelintasModel extends Model
{
    protected $table            = 'objek_melintas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'tipe_objek',
        'jumlah',
        'lokasi',
        'tanggal',
        'deskripsi',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [
        'tipe_objek' => 'required|in_list[truk,mobil,motor]',
        'jumlah' => 'required|integer|greater_than[0]',
        'lokasi' => 'required|min_length[3]|max_length[255]',
        'tanggal' => 'required|valid_date'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    // Get objek melintas with pagination
    public function getObjekMelintasList($page = 1, $perPage = 10, $search = '')
    {
        $builder = $this->select('objek_melintas.*, users.full_name as created_by_name')
                       ->join('users', 'users.id = objek_melintas.created_by', 'left')
                       ->orderBy('objek_melintas.tanggal', 'DESC');

        // Search functionality
        if ($search) {
            $builder->groupStart()
                    ->like('objek_melintas.lokasi', $search)
                    ->orLike('objek_melintas.tipe_objek', $search)
                    ->orLike('objek_melintas.deskripsi', $search)
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

    // Get objek melintas by ID with user info
    public function getObjekMelintasDetail($id)
    {
        return $this->select('objek_melintas.*, users.full_name as created_by_name')
                    ->join('users', 'users.id = objek_melintas.created_by', 'left')
                    ->where('objek_melintas.id', $id)
                    ->first();
    }
}
