<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Jogos de mata-mata não têm rodada (matchday) na API externa.
        Schema::table('matches', function (Blueprint $table) {
            $table->integer('game_day')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->integer('game_day')->nullable(false)->change();
        });
    }
};
