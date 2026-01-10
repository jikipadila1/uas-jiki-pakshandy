<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SensusSeeder extends Seeder
{
    public function run()
    {
        $data = [];
        $now  = date('Y-m-d H:i:s');

        for ($i = 1; $i <= 500; $i++) {
            // NIK sederhana: 16 digit dengan padding 0 di depan
            $nik = str_pad((string) $i, 16, '0', STR_PAD_LEFT);

            // Sebar tanggal input biar nggak sama semua
            $tanggalInput = date('Y-m-d', strtotime("2025-01-01 +$i days"));

            $data[] = [
                'nik'            => $nik,
                'nama_penduduk'  => "Penduduk $i",
                'alamat'         => "Jl. Contoh No. $i",
                // Asumsikan kita sudah punya 500 kota (id 1–500)
                'kota_id'        => rand(1, 500),
                'tanggal_input'  => $tanggalInput,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        foreach (array_chunk($data, 100) as $chunk) {
            $this->db->table('sensus')->insertBatch($chunk);
        }
    }
}