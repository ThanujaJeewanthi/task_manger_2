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

            ['name' => 'Common Dashboard', 'code' => '1.1', 'page_category_id' => 1, 'active' => true],
            ['name' => 'View page categories', 'code' => '2.1', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Create page category', 'code' => '2.2', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Edit page category', 'code' => '2.3', 'page_category_id' => 2, 'active' => true],
            ['name' => 'View pages', 'code' => '2.4', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Create page', 'code' => '2.5', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Edit page', 'code' => '2.6', 'page_category_id' => 2, 'active' => true],
            ['name' => 'View user roles', 'code' => '3.1', 'page_category_id' => 3, 'active' => true],
            ['name' => 'Create user role', 'code' => '3.2', 'page_category_id' => 3, 'active' => true],
            ['name' => 'Edit user role', 'code' => '3.3', 'page_category_id' => 3, 'active' => true],
            ['name' => 'Manage permissions', 'code' => '3.4', 'page_category_id' => 3, 'active' => true],
            ['name' => 'View users', 'code' => '4.1', 'page_category_id' => 4, 'active' => true],
            ['name' => 'Create user', 'code' => '4.2', 'page_category_id' => 4, 'active' => true],
            ['name' => 'Edit user', 'code' => '4.3', 'page_category_id' => 4, 'active' => true],
            ['name' => 'Records', 'code' => '5.1', 'page_category_id' => 5, 'active' => true],



        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(['code' => $page['code']], $page);
        }
    }
}
