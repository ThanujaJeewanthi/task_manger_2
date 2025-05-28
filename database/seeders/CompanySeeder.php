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
            'name' => 'ADD SOlutions Pvt Ltd',
            'address' => '175, 14A Lake View Drive,Colombo 5,Sri Lanka',
            'has_clients' => true,
            'phone' => '0779447221',
            'email' => 'info@addsolutions.lk',
            'website' => 'www.addsolutions.lk',

            'active' => true
        ]);
    }
}
