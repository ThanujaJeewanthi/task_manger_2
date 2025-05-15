<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    public function up()
{
    Schema::create('logs', function (Blueprint $table) {
        $table->id();

        $table->string('action');

        // Correct order: nullable before constrained
        $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
        $table->foreignId('user_role_id')->nullable()->constrained('user_roles')->onDelete('set null');

        $table->ipAddress('ip_address')->nullable();
        $table->text('description')->nullable();
        $table->boolean('active')->default(true);
        $table->timestamps();

         });
}
    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
