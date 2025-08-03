<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_users', function (Blueprint $table) {
            $table->decimal('original_duration', 8, 2)->nullable()->after('duration');
        });
    }

    public function down(): void
    {
        Schema::table('job_users', function (Blueprint $table) {
            $table->dropColumn('original_duration');
        });
    }
};
