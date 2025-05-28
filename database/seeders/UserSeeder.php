<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([

            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'phone_number' => '(555) 987-6543',
            'company_id' => 1,

            'user_role_id' => 1,
            'active' => true,
            'created_by' => null,
            'updated_by' => null
        ]);
    }
}
