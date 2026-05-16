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
        ]);
    }
}
