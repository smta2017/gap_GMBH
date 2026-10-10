<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Company;
use DB;

class CompanyUserSeeder extends Seeder
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

            $companies = Company::get();

            foreach($companies as $company)
            {
                $username = $company->type->name . ' user - ' . $company->name;
                
                $email = str_replace(' ', '', $username) . 'email@' . str_replace(' ', '', $company->name) . '.com';

                $checkUser = User::where('username', $username)->orWhere('email', $email)->first();

                if(!$checkUser)
                {
                    // User
                    $user = User::create([
                        'username' => $username,
                        'email' => $email,
                        'password' => bcrypt('password')
                    ]);
                
                    // User Details
                    $userDetails = UserDetails::create([
                        'user_id' => $user->id,
                        'first_name' => $company->type->name . ' user - ',
                        'last_name' => $company->name,
                        'mobile_number' => '01022351392480',
                        'fax' => '102999224',
                        'title' => 'Sir',
                        'department' => 'Dev',
                        'role_id' => 1,
                        'company_id' => $company->id
                    ]);
                }

            }

            DB::commit();

        }catch (\PDOException $e) {
            DB::rollBack();
        }
    }
}
