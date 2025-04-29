<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_role_details', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->foreignId('user_role_id')->constrained('user_roles')->onDelete('cascade');
            $table->foreignId('page_id')->constrained('pages')->onDelete('cascade');
            $table->foreignId('page_category_id')->constrained('page_categories')->onDelete('cascade');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::table('user_role_details', function (Blueprint $table) {
            $table->enum('status', ['allow', 'disallow'])->default('disallow');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_role_details');
    }
}
;
