<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserTrackingColumnsToTables extends Migration
{
    /**
     * Add created_by and updated_by to multiple tables
     */
    public function up()
    {
        $tables = [
            'user_role_details',
            'user_roles',
            'pages',
            'page_categories',
            'users',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
            });
        }
    }

    /**
     * Reverse the migration
     */
    public function down()
    {
        $tables = [
            'user_role_details',
            'user_roles',
            'pages',
            'page_categories',
            'users',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn(['created_by', 'updated_by']);
            });
        }
    }
}
