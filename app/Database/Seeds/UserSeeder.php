<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [];
        $now  = date('Y-m-d H:i:s');

        for ($i = 1; $i <= 500; $i++) {
            $data[] = [
                'name'          => "User $i",
                'email'         => "user{$i}@sensus.local",
                'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
                'role'          => $i === 1 ? 'admin' : 'user',
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        // Insert in batches to avoid memory issues
        foreach (array_chunk($data, 100) as $chunk) {
            $this->db->table('users')->insertBatch($chunk);
        }
    }
}