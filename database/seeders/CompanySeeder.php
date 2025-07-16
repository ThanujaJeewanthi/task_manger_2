<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function run()
    {
        
         Company::create([
            'name' => 'Cleanline Linen Management Pvt Ltd',
            'address' => '142/2, Wathuraboda Road,Narampola, Dekatana, Sri Lanka.',
            'has_clients' => true,
            'phone' => '0114 848 272',
            'email' => 'info@cleanline.lk',
            'website' => 'www.cleanline.lk',

            'active' => true
        ]);
    }
}
