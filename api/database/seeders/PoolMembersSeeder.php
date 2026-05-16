<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PoolMembersSeeder extends Seeder
{
    public function run(): void
    {
        $familiaPool = DB::table('pools')->where('join_code', 'FAM001')->value('id');
        $publicPool  = DB::table('pools')->where('join_code', 'PUB001')->value('id');

        $alice = DB::table('users')->where('email', 'alice@bolao.test')->value('id');
        $bob   = DB::table('users')->where('email', 'bob@bolao.test')->value('id');
        $carol = DB::table('users')->where('email', 'carol@bolao.test')->value('id');

        $userIds = DB::table('users')
            ->whereIn('email', array_map(fn($i) => 'user' . str_pad($i, 2, '0', STR_PAD_LEFT) . '@bolao.test', range(1, 10)))
            ->orderBy('email')
            ->pluck('id')
            ->all();

        $now  = now();
        $rows = [];

        // FAM001 — Alice (OWNER), Bob (ADMIN), Carol + user01-user10 (MEMBER)
        $rows[] = ['pool_id' => $familiaPool, 'user_id' => $alice, 'role' => 'OWNER', 'status' => 'ACTIVE', 'joined_at' => $now];
        $rows[] = ['pool_id' => $familiaPool, 'user_id' => $bob,   'role' => 'ADMIN', 'status' => 'ACTIVE', 'joined_at' => $now];
        $rows[] = ['pool_id' => $familiaPool, 'user_id' => $carol,  'role' => 'MEMBER', 'status' => 'ACTIVE', 'joined_at' => $now];
        foreach ($userIds as $uid) {
            $rows[] = ['pool_id' => $familiaPool, 'user_id' => $uid, 'role' => 'MEMBER', 'status' => 'ACTIVE', 'joined_at' => $now];
        }

        // PUB001 — Bob (OWNER), Alice + user01-user05 (MEMBER)
        $rows[] = ['pool_id' => $publicPool, 'user_id' => $bob,   'role' => 'OWNER',  'status' => 'ACTIVE', 'joined_at' => $now];
        $rows[] = ['pool_id' => $publicPool, 'user_id' => $alice, 'role' => 'MEMBER', 'status' => 'ACTIVE', 'joined_at' => $now];
        foreach (array_slice($userIds, 0, 5) as $uid) {
            $rows[] = ['pool_id' => $publicPool, 'user_id' => $uid, 'role' => 'MEMBER', 'status' => 'ACTIVE', 'joined_at' => $now];
        }

        DB::table('pool_members')->insert($rows);
    }
}
