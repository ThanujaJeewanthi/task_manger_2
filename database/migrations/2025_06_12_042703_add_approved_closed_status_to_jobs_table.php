<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            // Update the status enum to include 'approved' and 'closed'
            $table->enum('status', ['pending', 'approved', 'in_progress', 'on_hold', 'completed', 'closed', 'cancelled'])->change();

            // Add review fields for job closure
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('completed_date');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('review_notes')->nullable()->after('reviewed_at');
            $table->timestamp('closed_at')->nullable()->after('review_notes');
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->enum('status', ['pending', 'in_progress', 'on_hold', 'completed', 'cancelled'])->change();
            $table->dropColumn(['reviewed_by', 'reviewed_at', 'review_notes', 'closed_at']);
        });
    }
};
