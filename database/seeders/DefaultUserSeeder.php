<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\UserDetails;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Image;
use DB;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::beginTransaction();

            // User
            $user = User::create([
                'username' => 'admin_GG',
                'email' => 'email@gmail.com',
                'password' => bcrypt('78svccAsv')
            ]);
            
            // User Details
            $userDetails = UserDetails::create([
                'user_id' => $user->id,
                'first_name' => 'Joe',
                'last_name' => 'Dowe',
                'mobile_number' => '103242452313',
                'fax' => '10222455222',
                'title' => 'Sales Manager',
                'department' => 'Marketing',
                'role_id' => 1,
                'company_id' => 1,
            ]);

            DB::commit();

        }catch (\PDOException $e) {
            DB::rollBack();
        }
    }
}
