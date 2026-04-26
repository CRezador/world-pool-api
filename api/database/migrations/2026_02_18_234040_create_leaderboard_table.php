<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leaderboard', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pool_id')->constrained('pools')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('points')->default(0);
            $table->integer('exact_hits')->default(0);
            $table->integer('result_hits')->default(0);
            $table->integer('guesses_count')->default(0);
            $table->timestamp('updated_at')->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaderboard');
    }
};
