<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\TourOperator;
use DB;

class CompanySeeder extends Seeder
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

            $user = User::first();
            
            $companyCheck = Company::where('company_type_id', 1)->first();

            if($user && !$companyCheck)
            {
                $companyDB = Company::create([

                    'id' => 1,
                    'name' => 'Golf Globe Travel GmbH',
                    'phone' => '+49 511 300320 0',
                    
                    "website" => "https://www.golfglobe.com/",
                    "email" => "travel@golfglobe.com",
                    "delegate_user_id" => $user->id,
                    "assigned_user_id" => $user->id,
                        
                    'company_type_id' => 1,
            
                    "rank" => 7,
                    "contract" => 1,
    
                    'region_id' => 2,
                    'country_id' => 39,
                    'city_id' => 188,
                    'street' => 'Hanover, Lower Saxony',
                ]);
            }
            

            $companies = Company::withTrashed()->where('company_type_id', 1)->get();
            
            foreach($companies as $company)
            {
                $company->update([
                    'deleted_at' => null
                ]);
            }

            DB::commit();
            
        }catch (\PDOException $e) {
            DB::rollBack();
        }
    }
}
