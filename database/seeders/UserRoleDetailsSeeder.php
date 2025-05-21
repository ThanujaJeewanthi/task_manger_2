<?php

namespace Database\Seeders;

use App\Models\UserRoleDetail;
use App\Models\Page;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class UserRoleDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all pages and user roles
        $pages = Page::all();
        $roles = UserRole::all();

        // Create permissions for each role
        foreach ($roles as $role) {
            switch ($role->name) {
                case 'Super Admin':

                    foreach ($pages as $page) {
                        UserRoleDetail::create([
                            'user_role_id' => $role->id,
                            'page_id' => $page->id,
                            'page_category_id' => $page->page_category_id,
                            'code' => $page->code,
                            'active' => true,
                            'status' => 'allow'
                        ]);
                    }
                    break;

            }
        }


    }


}
