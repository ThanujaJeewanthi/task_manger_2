<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskUserAssignmentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('task_user_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('duration_in_days')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('assignment_notes')->nullable();
            $table->boolean('active')->default(true);

            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Ensure each user has only one assignment per task
            $table->unique(['task_id', 'user_id', 'active'], 'unique_active_task_user_assignment');
            $table->index(['job_id', 'user_id']);
            $table->index(['task_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_user_assignments');
    }
}
