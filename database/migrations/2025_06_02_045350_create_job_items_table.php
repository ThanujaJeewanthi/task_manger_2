<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobItemsTable extends Migration
{
    public function up()
    {
        Schema::create('job_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->nullable()->constrained()->onDelete('set null'); // For existing items
            $table->string('custom_item_description')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->text('notes')->nullable();
            $table->text('issue_description')->nullable();
            $table->enum('addition_stage', ['material_request', 'job_approval'])->default('job_approval');
            $table->foreignId('added_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('added_at');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_items');
    }
}
