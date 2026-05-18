<?php

namespace Database\Seeders;

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
            ['name' => 'Coreia do Sul',              'code' => 'KOR', 'flag_code' => 'kr',     'group_id' => $groupIdByLetter['A'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'República Tcheca',            'code' => 'CZE', 'flag_code' => 'cz',     'group_id' => $groupIdByLetter['A'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'México',                     'code' => 'MEX', 'flag_code' => 'mx',     'group_id' => $groupIdByLetter['A'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'África do Sul',              'code' => 'RSA', 'flag_code' => 'za',     'group_id' => $groupIdByLetter['A'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo B
            ['name' => 'Canadá',                     'code' => 'CAN', 'flag_code' => 'ca',     'group_id' => $groupIdByLetter['B'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Catar',                      'code' => 'QAT', 'flag_code' => 'qa',     'group_id' => $groupIdByLetter['B'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bósnia e Herzegovina',       'code' => 'BIH', 'flag_code' => 'ba',     'group_id' => $groupIdByLetter['B'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Suíça',                      'code' => 'SUI', 'flag_code' => 'ch',     'group_id' => $groupIdByLetter['B'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo C
            ['name' => 'Brasil',                     'code' => 'BRA', 'flag_code' => 'br',     'group_id' => $groupIdByLetter['C'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Escócia',                    'code' => 'SCO', 'flag_code' => 'gb-sct', 'group_id' => $groupIdByLetter['C'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Haiti',                      'code' => 'HAI', 'flag_code' => 'ht',     'group_id' => $groupIdByLetter['C'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marrocos',                   'code' => 'MAR', 'flag_code' => 'ma',     'group_id' => $groupIdByLetter['C'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo D
            ['name' => 'Austrália',                  'code' => 'AUS', 'flag_code' => 'au',     'group_id' => $groupIdByLetter['D'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Estados Unidos',             'code' => 'USA', 'flag_code' => 'us',     'group_id' => $groupIdByLetter['D'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Turquia',                    'code' => 'TUR', 'flag_code' => 'tr',     'group_id' => $groupIdByLetter['D'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Paraguai',                   'code' => 'PAR', 'flag_code' => 'py',     'group_id' => $groupIdByLetter['D'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo E
            ['name' => 'Alemanha',                   'code' => 'GER', 'flag_code' => 'de',     'group_id' => $groupIdByLetter['E'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Costa do Marfim',            'code' => 'CIV', 'flag_code' => 'ci',     'group_id' => $groupIdByLetter['E'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Curaçao',                    'code' => 'CUW', 'flag_code' => 'cw',     'group_id' => $groupIdByLetter['E'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Equador',                    'code' => 'ECU', 'flag_code' => 'ec',     'group_id' => $groupIdByLetter['E'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo F
            ['name' => 'Suécia',                     'code' => 'SWE', 'flag_code' => 'se',     'group_id' => $groupIdByLetter['F'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Holanda',                    'code' => 'NED', 'flag_code' => 'nl',     'group_id' => $groupIdByLetter['F'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Japão',                      'code' => 'JPN', 'flag_code' => 'jp',     'group_id' => $groupIdByLetter['F'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tunísia',                    'code' => 'TUN', 'flag_code' => 'tn',     'group_id' => $groupIdByLetter['F'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo G
            ['name' => 'Bélgica',                    'code' => 'BEL', 'flag_code' => 'be',     'group_id' => $groupIdByLetter['G'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Egito',                      'code' => 'EGY', 'flag_code' => 'eg',     'group_id' => $groupIdByLetter['G'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Irã',                        'code' => 'IRN', 'flag_code' => 'ir',     'group_id' => $groupIdByLetter['G'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Nova Zelândia',              'code' => 'NZL', 'flag_code' => 'nz',     'group_id' => $groupIdByLetter['G'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo H
            ['name' => 'Arábia Saudita',             'code' => 'KSA', 'flag_code' => 'sa',     'group_id' => $groupIdByLetter['H'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cabo Verde',                 'code' => 'CPV', 'flag_code' => 'cv',     'group_id' => $groupIdByLetter['H'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Espanha',                    'code' => 'ESP', 'flag_code' => 'es',     'group_id' => $groupIdByLetter['H'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Uruguai',                    'code' => 'URU', 'flag_code' => 'uy',     'group_id' => $groupIdByLetter['H'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo I
            ['name' => 'França',                     'code' => 'FRA', 'flag_code' => 'fr',     'group_id' => $groupIdByLetter['I'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Iraque',                     'code' => 'IRQ', 'flag_code' => 'iq',     'group_id' => $groupIdByLetter['I'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Noruega',                    'code' => 'NOR', 'flag_code' => 'no',     'group_id' => $groupIdByLetter['I'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Senegal',                    'code' => 'SEN', 'flag_code' => 'sn',     'group_id' => $groupIdByLetter['I'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo J
            ['name' => 'Argentina',                  'code' => 'ARG', 'flag_code' => 'ar',     'group_id' => $groupIdByLetter['J'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Argélia',                    'code' => 'ALG', 'flag_code' => 'dz',     'group_id' => $groupIdByLetter['J'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jordânia',                   'code' => 'JOR', 'flag_code' => 'jo',     'group_id' => $groupIdByLetter['J'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Áustria',                    'code' => 'AUT', 'flag_code' => 'at',     'group_id' => $groupIdByLetter['J'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo K
            ['name' => 'Colômbia',                   'code' => 'COL', 'flag_code' => 'co',     'group_id' => $groupIdByLetter['K'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'República Democrática do Congo', 'code' => 'COD', 'flag_code' => 'cd', 'group_id' => $groupIdByLetter['K'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Portugal',                   'code' => 'POR', 'flag_code' => 'pt',     'group_id' => $groupIdByLetter['K'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Uzbequistão',                'code' => 'UZB', 'flag_code' => 'uz',     'group_id' => $groupIdByLetter['K'], 'created_at' => now(), 'updated_at' => now()],

            // Grupo L
            ['name' => 'Croácia',                    'code' => 'CRO', 'flag_code' => 'hr',     'group_id' => $groupIdByLetter['L'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gana',                       'code' => 'GHA', 'flag_code' => 'gh',     'group_id' => $groupIdByLetter['L'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inglaterra',                 'code' => 'ENG', 'flag_code' => 'gb-eng', 'group_id' => $groupIdByLetter['L'], 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Panamá',                     'code' => 'PAN', 'flag_code' => 'pa',     'group_id' => $groupIdByLetter['L'], 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('teams')->insert($teams);
    }
}
