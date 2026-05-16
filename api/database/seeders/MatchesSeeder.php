<?php

namespace Database\Seeders;

use App\Http\Enums\MatchStage;
use App\Http\Enums\MatchStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatchesSeeder extends Seeder
{
    public function run(): void
    {
        $teams  = DB::table('teams')->pluck('id', 'code');
        $groups = DB::table('groups')->pluck('id', 'name');

        $now = now();
        $F   = MatchStatus::FINISHED->value;
        $S   = MatchStatus::SCHEDULED->value;
        $GS  = MatchStage::GROUP_STAGE->value;

        // Resultados das 7 partidas finalizadas:
        // 1. BRA 2-1 MEX  → vitória mandante
        // 2. ARG 1-1 FRA  → empate
        // 3. ESP 3-0 POR  → vitória mandante
        // 4. GHA 0-2 BEL  → vitória visitante
        // 5. JPN 1-2 COL  → vitória visitante
        // 6. KOR 0-0 AUS  → empate
        // 7. URU 2-0 CAN  → vitória mandante
        $matches = [
            ['game_day' => 1, 'kickoff_at' => $now->copy()->subDays(7),  'stage' => $GS, 'home_team_id' => $teams['BRA'], 'away_team_id' => $teams['MEX'], 'group_id' => $groups['C'], 'status' => $F, 'home_score' => 2, 'away_score' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['game_day' => 2, 'kickoff_at' => $now->copy()->subDays(6),  'stage' => $GS, 'home_team_id' => $teams['ARG'], 'away_team_id' => $teams['FRA'], 'group_id' => null,         'status' => $F, 'home_score' => 1, 'away_score' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['game_day' => 3, 'kickoff_at' => $now->copy()->subDays(5),  'stage' => $GS, 'home_team_id' => $teams['ESP'], 'away_team_id' => $teams['POR'], 'group_id' => $groups['H'], 'status' => $F, 'home_score' => 3, 'away_score' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['game_day' => 4, 'kickoff_at' => $now->copy()->subDays(4),  'stage' => $GS, 'home_team_id' => $teams['GHA'], 'away_team_id' => $teams['BEL'], 'group_id' => $groups['G'], 'status' => $F, 'home_score' => 0, 'away_score' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['game_day' => 5, 'kickoff_at' => $now->copy()->subDays(3),  'stage' => $GS, 'home_team_id' => $teams['JPN'], 'away_team_id' => $teams['COL'], 'group_id' => null,         'status' => $F, 'home_score' => 1, 'away_score' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['game_day' => 6, 'kickoff_at' => $now->copy()->subDays(2),  'stage' => $GS, 'home_team_id' => $teams['KOR'], 'away_team_id' => $teams['AUS'], 'group_id' => null,         'status' => $F, 'home_score' => 0, 'away_score' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['game_day' => 7, 'kickoff_at' => $now->copy()->subDays(1),  'stage' => $GS, 'home_team_id' => $teams['URU'], 'away_team_id' => $teams['CAN'], 'group_id' => null,         'status' => $F, 'home_score' => 2, 'away_score' => 0, 'created_at' => $now, 'updated_at' => $now],

            // Agendadas — disponíveis para criar palpites no Postman
            ['game_day' => 8, 'kickoff_at' => $now->copy()->addDays(1), 'stage' => $GS, 'home_team_id' => $teams['GER'], 'away_team_id' => $teams['ENG'], 'group_id' => null,         'status' => $S, 'home_score' => null, 'away_score' => null, 'created_at' => $now, 'updated_at' => $now],
            ['game_day' => 9, 'kickoff_at' => $now->copy()->addDays(2), 'stage' => $GS, 'home_team_id' => $teams['NED'], 'away_team_id' => $teams['BEL'], 'group_id' => $groups['F'], 'status' => $S, 'home_score' => null, 'away_score' => null, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('matches')->insert($matches);
    }
}
