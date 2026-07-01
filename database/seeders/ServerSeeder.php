<?php

namespace Database\Seeders;

use App\Models\Server;
use Illuminate\Database\Seeder;

class ServerSeeder extends Seeder
{
    public function run(): void
    {
        Server::insert([
            ['name' => 'v1', 'url' => 'https://zakerxa-zakerxa-v1.hf.space', 'role_access' => json_encode(['tester']), 'priority' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'v2', 'url' => 'https://zakerxav2-zakerxav2-v2.hf.space', 'role_access' => json_encode(['normal']), 'priority' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'v3', 'url' => 'https://zakerxav3-zakerxav3-v3.hf.space', 'role_access' => null, 'priority' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()], // fallback for all
            ['name' => 'recap', 'url' => 'https://recap.zakerxa.com', 'role_access' => json_encode(['pro']), 'priority' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}