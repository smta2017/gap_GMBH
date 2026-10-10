<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Role::count() == 0)
        {
            // Roles
            $admin_role = Role::create([
                'id' => '1',
                'name' => 'Administrator',
                'status' => 1,
            ]);

            $super_admin_role = Role::create([
                'id' => '2',
                'name' => 'Super Admin-test',
                'status' => 1,
            ]);  
            
            $super_admin_role = Role::create([
                'id' => '3',
                'name' => 'CMS User',
                'status' => 1,
            ]);

            $super_admin_role = Role::create([
                'id' => '4',
                'name' => 'Client',
                'status' => 1,
            ]);
        }
    }
}
