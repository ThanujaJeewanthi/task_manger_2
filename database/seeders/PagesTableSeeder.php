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
            // Dashboard permissions
            ['name' => 'Common Dashboard', 'code' => '1.1', 'page_category_id' => 1, 'active' => true],
            ['name' => 'Super Admin Dashboard', 'code' => '1.2', 'page_category_id' => 1, 'active' => true],
            ['name' => 'Admin Dashboard', 'code' => '1.3', 'page_category_id' => 1, 'active' => true],
            ['name' => 'Employee Dashboard', 'code' => '1.4', 'page_category_id' => 1, 'active' => true],
            ['name' => 'Engineer Dashboard', 'code' => '1.5', 'page_category_id' => 1, 'active' => true],
            ['name' => 'Technical Officer Dashboard', 'code' => '1.6', 'page_category_id' => 1, 'active' => true],
            ['name' => 'Supervisor Dashboard', 'code' => '1.7', 'page_category_id' => 1, 'active' => true],

            // Page Category permissions
            ['name' => 'View page categories', 'code' => '2.1', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Create page category', 'code' => '2.2', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Edit page category', 'code' => '2.3', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Delete page category', 'code' => '2.4', 'page_category_id' => 2, 'active' => true],
            ['name' => 'View pages', 'code' => '2.5', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Create page', 'code' => '2.6', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Edit page', 'code' => '2.7', 'page_category_id' => 2, 'active' => true],
            ['name' => 'Delete page', 'code' => '2.8', 'page_category_id' => 2, 'active' => true],

            // User Role permissions
            ['name' => 'View user roles', 'code' => '3.1', 'page_category_id' => 3, 'active' => true],
            ['name' => 'Create user role', 'code' => '3.2', 'page_category_id' => 3, 'active' => true],
            ['name' => 'Edit user role', 'code' => '3.3', 'page_category_id' => 3, 'active' => true],
            ['name' => 'Delete user role', 'code' => '3.4', 'page_category_id' => 3, 'active' => true],
            ['name' => 'Manage permissions', 'code' => '3.5', 'page_category_id' => 3, 'active' => true],

            // User permissions
            ['name' => 'View users', 'code' => '4.1', 'page_category_id' => 4, 'active' => true],
            ['name' => 'Create user', 'code' => '4.2', 'page_category_id' => 4, 'active' => true],
            ['name' => 'Edit user', 'code' => '4.3', 'page_category_id' => 4, 'active' => true],
            ['name' => 'Delete user', 'code' => '4.4', 'page_category_id' => 4, 'active' => true],
            ['name' => 'View user details', 'code' => '4.5', 'page_category_id' => 4, 'active' => true],


            // Company permissions
            ['name' => 'View companies', 'code' => '5.1', 'page_category_id' => 5, 'active' => true],
            ['name' => 'Create company', 'code' => '5.2', 'page_category_id' => 5, 'active' => true],
            ['name' => 'View company details', 'code' => '5.3', 'page_category_id' => 5, 'active' => true],
            ['name' => 'Edit company', 'code' => '5.4', 'page_category_id' => 5, 'active' => true],
            ['name' => 'Delete company', 'code' => '5.5', 'page_category_id' => 5, 'active' => true],

            // Employee permissions
            ['name' => 'View employees', 'code' => '6.1', 'page_category_id' => 6, 'active' => true],
            ['name' => 'Create employee', 'code' => '6.2', 'page_category_id' => 6, 'active' => true],
            ['name' => 'View employee details', 'code' => '6.3', 'page_category_id' => 6, 'active' => true],
            ['name' => 'Edit employee', 'code' => '6.4', 'page_category_id' => 6, 'active' => true],
            ['name' => 'Delete employee', 'code' => '6.5', 'page_category_id' => 6, 'active' => true],

            // Client permissions
            ['name' => 'View clients', 'code' => '7.1', 'page_category_id' => 7, 'active' => true],
            ['name' => 'Create client', 'code' => '7.2', 'page_category_id' => 7, 'active' => true],
            ['name' => 'Edit client', 'code' => '7.3', 'page_category_id' => 7, 'active' => true],
            ['name' => 'Delete client', 'code' => '7.4', 'page_category_id' => 7, 'active' => true],

            // Supplier permissions
            ['name' => 'View suppliers', 'code' => '8.1', 'page_category_id' => 8, 'active' => true],
            ['name' => 'Create supplier', 'code' => '8.2', 'page_category_id' => 8, 'active' => true],
            ['name' => 'Edit supplier', 'code' => '8.3', 'page_category_id' => 8, 'active' => true],
            ['name' => 'Delete supplier', 'code' => '8.4', 'page_category_id' => 8, 'active' => true],

            // Equipment permissions
            ['name' => 'View equipments', 'code' => '9.1', 'page_category_id' => 9, 'active' => true],
            ['name' => 'Create equipment', 'code' => '9.2', 'page_category_id' => 9, 'active' => true],
            ['name' => 'Edit equipment', 'code' => '9.3', 'page_category_id' => 9, 'active' => true],
            ['name' => 'Delete equipment', 'code' => '9.4', 'page_category_id' => 9, 'active' => true],

            // Item permissions
            ['name' => 'View items', 'code' => '10.1', 'page_category_id' => 10, 'active' => true],
            ['name' => 'Create item', 'code' => '10.2', 'page_category_id' => 10, 'active' => true],
            ['name' => 'Edit item', 'code' => '10.3', 'page_category_id' => 10, 'active' => true],
            ['name' => 'Delete item', 'code' => '10.4', 'page_category_id' => 10, 'active' => true],

            // Job permissions
            ['name' => 'View job types', 'code' => '11.1', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Create job type', 'code' => '11.2', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Edit job type', 'code' => '11.3', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Delete job type', 'code' => '11.4', 'page_category_id' => 11, 'active' => true],
            ['name' => 'View job options', 'code' => '11.5', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Create job option', 'code' => '11.6', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Edit job option', 'code' => '11.7', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Delete job option', 'code' => '11.8', 'page_category_id' => 11, 'active' => true],
            ['name' => 'View jobs', 'code' => '11.9', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Create job', 'code' => '11.10', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Edit job', 'code' => '11.11', 'page_category_id' => 11, 'active' => true],
            ['name' => 'View job details', 'code' => '11.12', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Delete job', 'code' => '11.13', 'page_category_id' => 11, 'active' => true],
            ['name' => 'View job tasks', 'code' => '11.14', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Create job task', 'code' => '11.15', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Edit job task', 'code' => '11.16', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Delete job task', 'code' => '11.17', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Add job items', 'code' => '11.18', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Job approval', 'code' => '11.19', 'page_category_id' => 11, 'active' => true],
            ['name' => 'View job assignments', 'code' => '11.20', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Assign job', 'code' => '11.21', 'page_category_id' => 11, 'active' => true],
            ['name' => 'View job assignment details', 'code' => '11.22', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Update job assignment status', 'code' => '11.23', 'page_category_id' => 11, 'active' => true],
            ['name' => 'View my assignments', 'code' => '11.24', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Copy job', 'code' => '11.25', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Extend job task', 'code' => '11.26', 'page_category_id' => 11, 'active' => true],
            ['name' => 'View job history', 'code' => '11.27', 'page_category_id' => 11, 'active' => true],
            ['name' => 'View job history details', 'code' => '11.28', 'page_category_id' => 11, 'active' => true],
            ['name' => 'export job history pdf', 'code' => '11.29', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Job review', 'code' => '11.30', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Start task', 'code' => '11.31', 'page_category_id' => 11, 'active' => true],
            ['name' => 'Complete task', 'code' => '11.32', 'page_category_id' => 11, 'active' => true],

            // Task Extension permissions
            ['name' => 'Request task extension', 'code' => '12.1', 'page_category_id' => 12, 'active' => true],
            ['name' => 'View my extension requests', 'code' => '12.2', 'page_category_id' => 12, 'active' => true],
            ['name' => 'View extension requests', 'code' => '12.3', 'page_category_id' => 12, 'active' => true],
            ['name' => 'View extension request details', 'code' => '12.4', 'page_category_id' => 12, 'active' => true],
            ['name' => 'Approve/reject extension', 'code' => '12.5', 'page_category_id' => 12, 'active' => true],
            ['name' => 'View pending extension count', 'code' => '12.6', 'page_category_id' => 12, 'active' => true],

            // Log permissions
            ['name' => 'View logs', 'code' => '13.1', 'page_category_id' => 13, 'active' => true],
            ['name' => 'Log details', 'code' => '13.2', 'page_category_id' => 13, 'active' => true],
            ['name' => 'Export logs', 'code' => '13.3', 'page_category_id' => 13, 'active' => true],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(['code' => $page['code']], $page);
        }
    }
}
