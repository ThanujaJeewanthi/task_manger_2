<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['user_roles', 'page_categories', 'pages','companies','user_role_details','job_users'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // Avoid duplicating columns
                if (!Schema::hasColumn($tableName, 'created_by')) {
                    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn($tableName, 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       $tables = ['user_roles', 'page_categories', 'pages','companies','user_role_details','job_users'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign([$table->getTable().'_created_by_foreign']);
                $table->dropForeign([$table->getTable().'_updated_by_foreign']);
                $table->dropColumn(['created_by', 'updated_by']);
            });
        }
    }
};
