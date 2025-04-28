<?php
namespace database\seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'admin',
            'client',
            'rider',
            'laundry'
        ];

        foreach ($roles as $role) {
            DB::table('user_roles')->insert([
                'name' => $role,
                //'slug' => Str::slug($role),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
