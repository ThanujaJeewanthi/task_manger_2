<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('job_number', 50);
            $table->foreignId('job_type_id')->constrained('job_types')->restrictOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('equipment_id')->nullable()->constrained('equipments')->nullOnDelete();
            $table->text('description')->nullable();
              $table->json('photos')->nullable();
            $table->text('references')->nullable();
            $table->enum('status', ['draft', 'pending', 'in_progress', 'on_hold', 'completed', 'cancelled'])->default('draft');
            $table->enum('priority', ['1', '2', '3', '4'])->default('2');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('completed_date')->nullable();

            $table->foreignId('tasks_added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('employees_added_by')->nullable()->constrained('users')->nullOnDelete();

            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->index('company_id');
            $table->index('job_number');
            $table->index('job_type_id');
            $table->index('client_id');

            $table->index('status');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('jobs');
    }
}
