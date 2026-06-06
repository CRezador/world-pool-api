<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leaderboard', function (Blueprint $table) {
            $table->unsignedInteger('previous_position')->nullable()->after('position');
        });
    }

    public function down(): void
    {
        Schema::table('leaderboard', function (Blueprint $table) {
            $table->dropColumn('previous_position');
        });
    }
};
