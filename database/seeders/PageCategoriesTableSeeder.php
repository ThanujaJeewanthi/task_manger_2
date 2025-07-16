<?php

namespace Database\Seeders;

use App\Models\PageCategory;
use Illuminate\Database\Seeder;

class PageCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['id' => 1, 'name' => 'Dashboard', 'active' => true],
            ['id' => 2, 'name' => 'Pages', 'active' => true],
            ['id' => 3, 'name' => 'Permissions', 'active' => true],

            ['id' => 4, 'name' => 'Users', 'active' => true],
             ['id' => 5, 'name' => 'Companies', 'active' => true],
            ['id' => 6, 'name' => 'Employee', 'active' => true],
            ['id' => 7, 'name' => 'Client', 'active' => true],
            ['id' => 8, 'name' => 'Supplier', 'active' => true],
            ['id' => 9, 'name' => 'Equipment', 'active' => true],
            ['id' => 10, 'name' => 'Item', 'active' => true],
            ['id' => 11, 'name' => 'Job', 'active' => true],
            ['id' => 12, 'name' => 'Task Extension', 'active' => true],
            ['id' => 13, 'name' => 'Logs', 'active' => true],





        ];

        foreach ($categories as $category) {
            PageCategory::updateOrCreate(['id' => $category['id']], $category);
        }
    }
}
