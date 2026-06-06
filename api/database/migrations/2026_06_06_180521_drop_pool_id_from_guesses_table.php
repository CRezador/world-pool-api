<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('guesses', function (Blueprint $table) {
            $table->dropForeign(['pool_id']);
            $table->dropUnique('guesses_pool_id_user_id_match_id_unique');
            $table->dropColumn('pool_id');
            $table->unique(['user_id', 'match_id']);
        });
    }

    public function down(): void
    {
        Schema::table('guesses', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'match_id']);
            $table->foreignId('pool_id')->constrained('pools')->onDelete('cascade');
            $table->unique(['pool_id', 'user_id', 'match_id']);
        });
    }
};
