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
            ['id' => 2, 'name' => 'Management', 'active' => true],
            ['id' => 3, 'name' => 'Client', 'active' => true],
            ['id' => 4, 'name' => 'Rider', 'active' => true],
            ['id' => 5, 'name' => 'Laundry', 'active' => true],
            ['id' => 6, 'name' => 'Order', 'active' => true],

        ];

        foreach ($categories as $category) {
            PageCategory::updateOrCreate(['id' => $category['id']], $category);
        }
    }
}
