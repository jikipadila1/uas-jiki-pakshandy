<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run()
    {
        $data = [];
        $now  = date('Y-m-d H:i:s');

        for ($i = 1; $i <= 500; $i++) {
            $data[] = [
                'nama_kota'  => "Kota $i",
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($data, 100) as $chunk) {
            $this->db->table('cities')->insertBatch($chunk);
        }
    }
}