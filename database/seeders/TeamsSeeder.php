<?php

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
            ['name' => 'Coreia do Sul',      'code' => 'KOR', 'group_id' => $groupIdByLetter['A']],
            ['name' => 'Europa D',           'code' => 'EUD', 'group_id' => $groupIdByLetter['A']],
            ['name' => 'México',             'code' => 'MEX', 'group_id' => $groupIdByLetter['A']],
            ['name' => 'África do Sul',      'code' => 'RSA', 'group_id' => $groupIdByLetter['A']],

            // Grupo B
            ['name' => 'Canadá',             'code' => 'CAN', 'group_id' => $groupIdByLetter['B']],
            ['name' => 'Catar',              'code' => 'QAT', 'group_id' => $groupIdByLetter['B']],
            ['name' => 'Europa A',           'code' => 'EUA', 'group_id' => $groupIdByLetter['B']],
            ['name' => 'Suíça',              'code' => 'SUI', 'group_id' => $groupIdByLetter['B']],

            // Grupo C
            ['name' => 'Brasil',             'code' => 'BRA', 'group_id' => $groupIdByLetter['C']],
            ['name' => 'Escócia',            'code' => 'SCO', 'group_id' => $groupIdByLetter['C']],
            ['name' => 'Haiti',              'code' => 'HAI', 'group_id' => $groupIdByLetter['C']],
            ['name' => 'Marrocos',           'code' => 'MAR', 'group_id' => $groupIdByLetter['C']],

            // Grupo D
            ['name' => 'Austrália',          'code' => 'AUS', 'group_id' => $groupIdByLetter['D']],
            ['name' => 'Estados Unidos',     'code' => 'USA', 'group_id' => $groupIdByLetter['D']],
            ['name' => 'Europa C',           'code' => 'EUC', 'group_id' => $groupIdByLetter['D']],
            ['name' => 'Paraguai',           'code' => 'PAR', 'group_id' => $groupIdByLetter['D']],

            // Grupo E
            ['name' => 'Alemanha',           'code' => 'GER', 'group_id' => $groupIdByLetter['E']],
            ['name' => 'Costa do Marfim',    'code' => 'CIV', 'group_id' => $groupIdByLetter['E']],
            ['name' => 'Curaçao',            'code' => 'CUW', 'group_id' => $groupIdByLetter['E']],
            ['name' => 'Equador',            'code' => 'ECU', 'group_id' => $groupIdByLetter['E']],

            // Grupo F
            ['name' => 'Europa B',           'code' => 'EUB', 'group_id' => $groupIdByLetter['F']],
            ['name' => 'Holanda',            'code' => 'NED', 'group_id' => $groupIdByLetter['F']],
            ['name' => 'Japão',              'code' => 'JPN', 'group_id' => $groupIdByLetter['F']],
            ['name' => 'Tunísia',            'code' => 'TUN', 'group_id' => $groupIdByLetter['F']],

            // Grupo G
            ['name' => 'Bélgica',            'code' => 'BEL', 'group_id' => $groupIdByLetter['G']],
            ['name' => 'Egito',              'code' => 'EGY', 'group_id' => $groupIdByLetter['G']],
            ['name' => 'Irã',                'code' => 'IRN', 'group_id' => $groupIdByLetter['G']],
            ['name' => 'Nova Zelândia',      'code' => 'NZL', 'group_id' => $groupIdByLetter['G']],

            // Grupo H
            ['name' => 'Arábia Saudita',     'code' => 'KSA', 'group_id' => $groupIdByLetter['H']],
            ['name' => 'Cabo Verde',         'code' => 'CPV', 'group_id' => $groupIdByLetter['H']],
            ['name' => 'Espanha',            'code' => 'ESP', 'group_id' => $groupIdByLetter['H']],
            ['name' => 'Uruguai',            'code' => 'URU', 'group_id' => $groupIdByLetter['H']],

            // Grupo I
            ['name' => 'França',             'code' => 'FRA', 'group_id' => $groupIdByLetter['I']],
            ['name' => 'Intercontinental 2', 'code' => 'IC2', 'group_id' => $groupIdByLetter['I']],
            ['name' => 'Noruega',            'code' => 'NOR', 'group_id' => $groupIdByLetter['I']],
            ['name' => 'Senegal',            'code' => 'SEN', 'group_id' => $groupIdByLetter['I']],

            // Grupo J
            ['name' => 'Argentina',          'code' => 'ARG', 'group_id' => $groupIdByLetter['J']],
            ['name' => 'Argélia',            'code' => 'ALG', 'group_id' => $groupIdByLetter['J']],
            ['name' => 'Jordânia',           'code' => 'JOR', 'group_id' => $groupIdByLetter['J']],
            ['name' => 'Áustria',            'code' => 'AUT', 'group_id' => $groupIdByLetter['J']],

            // Grupo K
            ['name' => 'Colômbia',           'code' => 'COL', 'group_id' => $groupIdByLetter['K']],
            ['name' => 'Intercontinental 1', 'code' => 'IC1', 'group_id' => $groupIdByLetter['K']],
            ['name' => 'Portugal',           'code' => 'POR', 'group_id' => $groupIdByLetter['K']],
            ['name' => 'Uzbequistão',        'code' => 'UZB', 'group_id' => $groupIdByLetter['K']],

            // Grupo L
            ['name' => 'Croácia',            'code' => 'CRO', 'group_id' => $groupIdByLetter['L']],
            ['name' => 'Gana',               'code' => 'GHA', 'group_id' => $groupIdByLetter['L']],
            ['name' => 'Inglaterra',         'code' => 'ENG', 'group_id' => $groupIdByLetter['L']],
            ['name' => 'Panamá',             'code' => 'PAN', 'group_id' => $groupIdByLetter['L']],
        ];

        DB::table('teams')->insert($teams);
    }
}
