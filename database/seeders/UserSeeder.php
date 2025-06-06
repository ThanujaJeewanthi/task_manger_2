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



            'email_verified_at' => now(),

            'username' => env('ADMIN_USERNAME'),
            'email' => env('ADMIN_EMAIL'),
            'password' => Hash::make(env('ADMIN_PASSWORD')),
            'phone_number' => env('ADMIN_PHONE'),
            'company_id' => 1,

            'user_role_id' => 1,
            'active' => true,
            'created_by' => null,
            'updated_by' => null
        ]);
    }
}
