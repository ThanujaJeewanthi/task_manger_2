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
                case 'admin':
                    // Admin has access to all pages
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

                case 'client':
                    // Client access - Common Dashboard + Client specific pages
                    $this->createPermissionForCode($role->id, '1.1'); // Common Dashboard
                    $this->createPermissionForCode($role->id, '1.3'); // Client Dashboard
                    $this->createPermissionForCode($role->id, '3.1'); // Client Dashboard




                    // Order pages
                    $this->createPermissionForCode($role->id, '6.1'); // Create Order
                    $this->createPermissionForCode($role->id, '6.3'); // View Orders
                    break;

                case 'rider':
                    // Rider access - Common Dashboard + Rider specific pages
                    $this->createPermissionForCode($role->id, '1.1'); // Common Dashboard
                    $this->createPermissionForCode($role->id, '1.4'); // Rider Dashboard


                    // Order view access
                    $this->createPermissionForCode($role->id, '6.4'); // View Orders
                    break;

                case 'laundry':
                    // Laundry access - Common Dashboard + Laundry specific pages
                    $this->createPermissionForCode($role->id, '1.1'); // Common Dashboard
                    $this->createPermissionForCode($role->id, '1.5'); // Laundry Dashboard

                    // Laundry specific pages (5.x series)


                    // Order access
                    $this->createPermissionForCode($role->id, '5.1'); // Edit Order
                    $this->createPermissionForCode($role->id, '5.2'); // View Orders
                    break;
            }
        }

        $this->command->info('User role details seeded successfully.');
    }

    /**
     * Helper function to create permission for a specific code
     *
     * @param int $roleId
     * @param string $code
     * @return void
     */
    private function createPermissionForCode($roleId, $code)
    {
        $page = Page::where('code', $code)->first();

        if ($page) {
            UserRoleDetail::create([
                'user_role_id' => $roleId,
                'page_id' => $page->id,
                'page_category_id' => $page->page_category_id,
                'code' => $page->code,
                'active' => true,
                'status' => 'allow'
            ]);
        }
    }
}
