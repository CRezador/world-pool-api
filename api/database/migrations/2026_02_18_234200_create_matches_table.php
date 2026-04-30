<?php

use App\Http\Enums\MatchStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Http\Enums\MatchStage;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->integer('game_day');
            $table->timestamp('kickoff_at')->nullable();
            $table->enum('stage', array_column(MatchStage::cases(), 'value'))->default(MatchStage::GROUP_STAGE->value);
            $table->foreignId('home_team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('away_team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('set null');
            $table->enum('status', array_column(MatchStatus::cases(), 'value'))->default(MatchStatus::SCHEDULED->value);
            $table->integer('home_score')->default(0)->nullable();
            $table->integer('away_score')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
