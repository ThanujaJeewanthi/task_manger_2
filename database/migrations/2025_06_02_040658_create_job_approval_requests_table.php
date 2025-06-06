<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobApprovalRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('job_approval_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approval_user_id')->constrained('users');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('request_notes')->nullable();
            $table->text('approval_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_approval_requests');
    }
}

// Also add these columns to jobs table
class AddItemManagementColumnsToJobsTable extends Migration
{
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->text('issue_description')->nullable()->after('description');
            $table->text('requested_new_items')->nullable()->after('issue_description');
            $table->text('completion_notes')->nullable()->after('completed_date');
        });
    }

    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn(['issue_description', 'requested_new_items', 'completion_notes']);
        });
    }
}
