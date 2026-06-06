<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leaderboard', function (Blueprint $table) {
            $table->unsignedInteger('rank')->nullable()->after('guesses_count');
        });
    }

    public function down(): void
    {
        Schema::table('leaderboard', function (Blueprint $table) {
            $table->dropColumn('rank');
        });
    }
};
