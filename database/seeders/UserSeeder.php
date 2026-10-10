<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserDetails;
use DB;

class UserSeeder extends Seeder
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
                'username' => 'aya_ashraf',
                'email' => 'aya_ashraf@gmail.com',
                'password' => bcrypt('Aa1234567')
            ]);
        
            // User Details
            $userDetails3 = UserDetails::create([
                'user_id' => $user->id,
                'first_name' => 'Aya',
                'last_name' => 'Ashraf',
                'mobile_number' => '01061220000',
                'fax' => '102338600',
                'title' => 'Quality',
                'department' => 'Control',
                'role_id' => 1,
                'company_id' => 1
            ]);
             
    

            // User
            $user = User::create([
                'username' => 'Helena_GG',
                'email' => 'hh@golfglobe.com',
                'password' => bcrypt('445464$Q54#48478svccAsv')
            ]);
        
            // User Details
            $userDetails10 = UserDetails::create([
                'user_id' => $user->id,
                'first_name' => 'Helena',
                'last_name' => 'Hemberger',
                'mobile_number' => '00141220000',
                'fax' => '889378923',
                'title' => 'Control',
                'department' => 'Agent',
                'role_id' => 1,
                'company_id' => 1
            ]);

            DB::commit();

        }catch (\PDOException $e) {
            DB::rollBack();
        }
    }
}
