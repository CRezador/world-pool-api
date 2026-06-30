<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Mata-mata: placar das cobranças de pênalti e o time vencedor do confronto.
     * `home_score`/`away_score` continuam guardando o placar do tempo normal/prorrogação;
     * o desempate por pênaltis e o vencedor passam a ter colunas próprias.
     */
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->integer('home_penalties')->nullable()->after('away_score');
            $table->integer('away_penalties')->nullable()->after('home_penalties');
            $table->foreignId('winner_team_id')->nullable()->after('away_penalties')
                ->constrained('teams')->onDelete('set null');
        });

        Schema::table('guesses', function (Blueprint $table) {
            // Time que o usuário palpita como vencedor do confronto (mata-mata).
            $table->foreignId('winner_team_id')->nullable()->after('away_score')
                ->constrained('teams')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('guesses', function (Blueprint $table) {
            $table->dropForeign(['winner_team_id']);
            $table->dropColumn('winner_team_id');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['winner_team_id']);
            $table->dropColumn(['home_penalties', 'away_penalties', 'winner_team_id']);
        });
    }
};
