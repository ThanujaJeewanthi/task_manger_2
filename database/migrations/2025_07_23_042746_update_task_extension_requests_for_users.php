<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTaskExtensionRequestsForUsers extends Migration
{
    public function up(): void
    {
        Schema::table('task_extension_requests', function (Blueprint $table) {
            // Add user_id column for new user-based assignments
            $table->foreignId('user_id')->nullable()->after('employee_id')->constrained('users')->cascadeOnDelete();

            // Make employee_id nullable to support both systems during transition
            $table->foreignId('employee_id')->nullable()->change();

            // Add index for user_id
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('task_extension_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropColumn('user_id');

            // Restore employee_id as required
            $table->foreignId('employee_id')->nullable(false)->change();
        });
    }
}
