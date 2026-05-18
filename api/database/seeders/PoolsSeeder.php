<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PoolsSeeder extends Seeder
{
    public function run(): void
    {
        $alice = DB::table('users')->where('email', 'alice@bolao.test')->value('id');
        $bob   = DB::table('users')->where('email', 'bob@bolao.test')->value('id');
        $carol = DB::table('users')->where('email', 'carol@bolao.test')->value('id');
        $admin = DB::table('users')->where('email', 'admin@bolao.test')->value('id');

        DB::table('pools')->insert([
            [
                'name'       => 'Bolão da Família',
                'join_code'  => 'FAM001',
                'owner_id'   => $alice,
                'is_public'  => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Bolão Público',
                'join_code'  => 'PUB001',
                'owner_id'   => $bob,
                'is_public'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Bolão do Trampo',
                'join_code'  => 'PUB002',
                'owner_id'   => $carol,
                'is_public'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Galera da Quadra',
                'join_code'  => 'PUB003',
                'owner_id'   => $alice,
                'is_public'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Os Cravadores',
                'join_code'  => 'PUB004',
                'owner_id'   => $bob,
                'is_public'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Bolão da Copa 2026',
                'join_code'  => 'PUB005',
                'owner_id'   => $admin,
                'is_public'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Família & Amigos',
                'join_code'  => 'PUB006',
                'owner_id'   => $carol,
                'is_public'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Palpiteiros Profissionais',
                'join_code'  => 'PUB007',
                'owner_id'   => $alice,
                'is_public'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
