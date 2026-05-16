<?php

namespace Database\Seeders;

use App\Http\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@bolao.test',
            'password' => Hash::make('password'),
            'role'     => UserRole::ADMIN->value,
        ]);

        User::factory()->create([
            'name'     => 'Alice',
            'email'    => 'alice@bolao.test',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'name'     => 'Bob',
            'email'    => 'bob@bolao.test',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'name'     => 'Carol',
            'email'    => 'carol@bolao.test',
            'password' => Hash::make('password'),
        ]);

        for ($i = 1; $i <= 10; $i++) {
            $n = str_pad($i, 2, '0', STR_PAD_LEFT);
            User::factory()->create([
                'name'     => "User{$n}",
                'email'    => "user{$n}@bolao.test",
                'password' => Hash::make('password'),
            ]);
        }
    }
}
