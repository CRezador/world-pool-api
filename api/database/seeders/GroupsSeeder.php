<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = collect(range('A', 'L'))->map(fn ($l) => [
            'name' => $l,
        ])->all();

        // como name é UNIQUE, isso insere A..L e, se já existir, só atualiza updated_at
        DB::table('groups')->upsert($rows, ['name']);
    }
}
