<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leaderboard', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable()->after('id');
            $table->timestamp('archived_at')->nullable()->after('updated_at');
            $table->unique(['pool_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('leaderboard', function (Blueprint $table) {
            $table->dropUnique(['pool_id', 'user_id']);
            $table->dropColumn(['created_at', 'archived_at']);
        });
    }
};
