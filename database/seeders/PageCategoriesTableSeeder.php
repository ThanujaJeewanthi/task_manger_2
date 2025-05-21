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
             ['id' => 5, 'name' => 'Records', 'active' => true],


        ];

        foreach ($categories as $category) {
            PageCategory::updateOrCreate(['id' => $category['id']], $category);
        }
    }
}
