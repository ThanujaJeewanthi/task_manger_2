<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pages = [
            // Dashboard pages
            ['name' => 'Common Dashboard', 'code' => '1.1', 'page_category_id' => 1, 'active' => true],
            ['name' => 'Admin Dashboard', 'code' => '1.2', 'page_category_id' => 1, 'active' => true],
            ['name' => 'Client Dashboard', 'code' => '1.3', 'page_category_id' => 1, 'active' => true],
            ['name' => 'Rider Dashboard', 'code' => '1.4', 'page_category_id' => 1, 'active' => true],
            ['name' => 'Laundry Dashboard', 'code' => '1.5', 'page_category_id' => 1, 'active' => true],

            // Management pages
            ['name' => 'User Management', 'code' => '2.1', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Role Management', 'code' => '2.2', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Permission Management', 'code' => '2.3', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Page Categories', 'code' => '2.4', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Create Page Category', 'code' => '2.5', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Edit Page Category', 'code' => '2.6', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Pages List', 'code' => '2.7', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Create Page', 'code' => '2.8', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Edit Page', 'code' => '2.9', 'page_category_id' => 2, 'active' => true],


            // Client pages
            ['name' => 'Create Client', 'code' => '3.1', 'page_category_id' => 3, 'active' => true],
            ['name' => 'View Clients', 'code' => '3.2', 'page_category_id' => 3, 'active' => true],
            ['name' => 'Edit Client', 'code' => '3.3', 'page_category_id' => 3, 'active' => true],



            // Rider pages

            ['name' => 'Create Rider', 'code' => '4.1', 'page_category_id' => 4, 'active' => true],
            ['name' => 'Edit Rider', 'code' => '4.2', 'page_category_id' => 4, 'active' => true],
            ['name' => 'View Riders', 'code' => '4.3', 'page_category_id' => 4, 'active' => true],


            // Laundry pages

            ['name' => 'Create Laundry', 'code' => '5.1', 'page_category_id' => 5, 'active' => true],
            ['name' => 'Edit Laundry', 'code' => '5.2', 'page_category_id' => 5, 'active' => true],
            ['name' => 'View Laundries', 'code' => '5.3', 'page_category_id' => 5, 'active' => true],

            //Order Pages
            ['name' => 'Create Order', 'code' => '6.1', 'page_category_id' => 6, 'active' => true],
            ['name' => 'Edit Order', 'code' => '6.2', 'page_category_id' => 6, 'active' => true],
            ['name' => 'View Orders', 'code' => '6.3', 'page_category_id' => 6, 'active' => true],

        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(['code' => $page['code']], $page);
        }
    }
}
