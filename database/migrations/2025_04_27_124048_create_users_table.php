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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['admin', 'user'])->default('admin');
            $table->string('username');
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone_number');
            $table->foreignId('company_id')->nullable()->constrained('companies','id')->nullOnDelete();
            $table->string('profile_picture')->nullable();
            $table->foreignId('user_role_id')->constrained('user_roles')->onDelete('cascade');
            $table->rememberToken();
            $table->boolean('active')->default(true);
            $table->timestamps();
             $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
