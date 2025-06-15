<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     */
  public function up(): void
{
    Schema::create('job_assignments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

        // Changed this line:
        $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();

        $table->enum('assignment_type', ['primary', 'secondary', 'supervisor', 'reviewer'])->default('primary');
        $table->date('assigned_date')->default(now());
        $table->date('due_date')->nullable();
        $table->enum('status', ['assigned', 'accepted', 'in_progress', 'completed', 'rejected'])->default('assigned');
        $table->text('notes')->nullable();
        $table->text('assignment_notes')->nullable();
        $table->boolean('can_assign_tasks')->default(false);
        $table->boolean('active')->default(true);
        $table->timestamps();
        $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

        // Changed the unique index to avoid potential issues
        $table->unique(['job_id', 'assignment_type', 'active'], 'unique_primary_assignment');
        $table->index(['user_id', 'status']);
        $table->index(['job_id', 'assignment_type']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_assignments');
    }
}
