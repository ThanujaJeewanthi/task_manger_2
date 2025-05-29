<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobOptionValuesTable extends Migration
{
    public function up()
    {
        Schema::create('job_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->foreignId('job_option_id')->constrained('job_options')->cascadeOnDelete();
            $table->string('value')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->unique(['job_id', 'job_option_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_option_values');
    }
}
