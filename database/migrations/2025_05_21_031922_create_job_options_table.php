<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_options', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('option_type', ['text', 'number', 'date', 'select', 'checkbox', 'file'])->default('text');
            $table->longText('options_json')->nullable();
            $table->boolean('required')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();


        });

        Schema::create('job_type_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_type_id')->constrained('job_types')->cascadeOnDelete();
            $table->foreignId('job_option_id')->constrained('job_options')->cascadeOnDelete();
            // $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->unique(['job_type_id', 'job_option_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_type_options');
        Schema::dropIfExists('job_options');
    }
}
