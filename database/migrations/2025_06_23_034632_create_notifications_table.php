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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['success', 'warning', 'danger', 'info', 'primary'])->default('info');
            $table->string('icon')->nullable();
            $table->json('data')->nullable(); // For additional data like job_id, task_id, etc.
            $table->string('action_url')->nullable(); // URL to navigate when clicked
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_important')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
