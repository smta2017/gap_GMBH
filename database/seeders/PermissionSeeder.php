<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

         

        \DB::select("update permissions set code = '1672753414659' where code = '1674061236739'");

         


        if(Permission::count() == 0)
        {
            try {
                // DB::beginTransaction();
                
                
                $path = base_path().'/public/backend/seeders/permissions_updated.json';
                
                $json = json_decode(file_get_contents($path), true);
                 
                $role = Role::find(1);
                
                foreach($json['permissions'] as $permission)
                {
                    $permissionData = Permission::create([
                        'id' => $permission['id'],
                        'name' => $permission['name'],
                        'code'  => $permission['code'],
                        'description' => $permission['description'],
                        'status' => $permission['status'],
                        'module_id' => $permission['module_id'],
                        'page_id'  => $permission['page_id'],
                    ]);
                    
                    if($role)
                    {
                        $role->permissions()->save($permissionData);
                    }
                    
                }
                
                // DB::commit();
                
    
            }catch (\PDOException $e) {
                DB::rollBack();
            }
        }
    }
}
