<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateAllTablesSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        $tables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $key = "Tables_in_{$dbName}";

        foreach ($tables as $table) {
            $tableName = $table->$key;
            if ($tableName !== 'migrations') {
                DB::table($tableName)->truncate(); // This also resets auto-increment
                echo "Truncated: $tableName\n";
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
