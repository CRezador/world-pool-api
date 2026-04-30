<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pool_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pool_id')->constrained('pools')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['MEMBER', 'ADMIN', 'OWNER'])->default('MEMBER');
            $table->enum('status', ['ACTIVE', 'LEFT', 'BANNED'])->default('ACTIVE');
            $table->timestamp('joined_at')->useCurrent();
            $table->unique(['pool_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pool_members');
    }
};
