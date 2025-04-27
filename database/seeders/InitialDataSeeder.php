<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserRole;
use App\Models\PageCategory;
use App\Models\Page;
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    public function run()
    {


        // Create user roles
        $userRoles = [
            ['id' => 1, 'name' => 'admin'],
            ['id' => 2, 'name' => 'client'],
            ['id' => 3, 'name' => 'laundry'],
            ['id' => 4, 'name' => 'rider'],
        ];


        foreach ($userRoles as $role) {
            UserRole::create($role);
        }



    }
}
