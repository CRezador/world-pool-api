<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groupIdByLetter = DB::table('groups')
            ->whereIn('name', range('A', 'L'))
            ->pluck('id', 'name');

        $teams = [
            // Grupo A
            ['name' => 'Coreia do Sul',      'code' => 'KOR', 'group_id' => $groupIdByLetter['A'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Europa D',           'code' => 'EUD', 'group_id' => $groupIdByLetter['A'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'México',             'code' => 'MEX', 'group_id' => $groupIdByLetter['A'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'África do Sul',      'code' => 'RSA', 'group_id' => $groupIdByLetter['A'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo B
            ['name' => 'Canadá',             'code' => 'CAN', 'group_id' => $groupIdByLetter['B'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Catar',              'code' => 'QAT', 'group_id' => $groupIdByLetter['B'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Europa A',           'code' => 'EUA', 'group_id' => $groupIdByLetter['B'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Suíça',              'code' => 'SUI', 'group_id' => $groupIdByLetter['B'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo C
            ['name' => 'Brasil',             'code' => 'BRA', 'group_id' => $groupIdByLetter['C'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Escócia',            'code' => 'SCO', 'group_id' => $groupIdByLetter['C'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Haiti',              'code' => 'HAI', 'group_id' => $groupIdByLetter['C'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marrocos',           'code' => 'MAR', 'group_id' => $groupIdByLetter['C'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo D
            ['name' => 'Austrália',          'code' => 'AUS', 'group_id' => $groupIdByLetter['D'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Estados Unidos',     'code' => 'USA', 'group_id' => $groupIdByLetter['D'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Europa C',           'code' => 'EUC', 'group_id' => $groupIdByLetter['D'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Paraguai',           'code' => 'PAR', 'group_id' => $groupIdByLetter['D'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo E
            ['name' => 'Alemanha',           'code' => 'GER', 'group_id' => $groupIdByLetter['E'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Costa do Marfim',    'code' => 'CIV', 'group_id' => $groupIdByLetter['E'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Curaçao',            'code' => 'CUW', 'group_id' => $groupIdByLetter['E'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Equador',            'code' => 'ECU', 'group_id' => $groupIdByLetter['E'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo F
            ['name' => 'Europa B',           'code' => 'EUB', 'group_id' => $groupIdByLetter['F'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Holanda',            'code' => 'NED', 'group_id' => $groupIdByLetter['F'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Japão',              'code' => 'JPN', 'group_id' => $groupIdByLetter['F'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tunísia',            'code' => 'TUN', 'group_id' => $groupIdByLetter['F'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo G
            ['name' => 'Bélgica',            'code' => 'BEL', 'group_id' => $groupIdByLetter['G'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Egito',              'code' => 'EGY', 'group_id' => $groupIdByLetter['G'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Irã',                'code' => 'IRN', 'group_id' => $groupIdByLetter['G'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Nova Zelândia',      'code' => 'NZL', 'group_id' => $groupIdByLetter['G'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo H
            ['name' => 'Arábia Saudita',     'code' => 'KSA', 'group_id' => $groupIdByLetter['H'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cabo Verde',         'code' => 'CPV', 'group_id' => $groupIdByLetter['H'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Espanha',            'code' => 'ESP', 'group_id' => $groupIdByLetter['H'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Uruguai',            'code' => 'URU', 'group_id' => $groupIdByLetter['H'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo I
            ['name' => 'França',             'code' => 'FRA', 'group_id' => $groupIdByLetter['I'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Intercontinental 2', 'code' => 'IC2', 'group_id' => $groupIdByLetter['I'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Noruega',            'code' => 'NOR', 'group_id' => $groupIdByLetter['I'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Senegal',            'code' => 'SEN', 'group_id' => $groupIdByLetter['I'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo J
            ['name' => 'Argentina',          'code' => 'ARG', 'group_id' => $groupIdByLetter['J'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Argélia',            'code' => 'ALG', 'group_id' => $groupIdByLetter['J'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jordânia',           'code' => 'JOR', 'group_id' => $groupIdByLetter['J'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Áustria',            'code' => 'AUT', 'group_id' => $groupIdByLetter['J'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo K
            ['name' => 'Colômbia',           'code' => 'COL', 'group_id' => $groupIdByLetter['K'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Intercontinental 1', 'code' => 'IC1', 'group_id' => $groupIdByLetter['K'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Portugal',           'code' => 'POR', 'group_id' => $groupIdByLetter['K'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Uzbequistão',        'code' => 'UZB', 'group_id' => $groupIdByLetter['K'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo L
            ['name' => 'Croácia',            'code' => 'CRO', 'group_id' => $groupIdByLetter['L'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gana',               'code' => 'GHA', 'group_id' => $groupIdByLetter['L'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inglaterra',         'code' => 'ENG', 'group_id' => $groupIdByLetter['L'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Panamá',             'code' => 'PAN', 'group_id' => $groupIdByLetter['L'], 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('teams')->insert($teams);
    }
}
