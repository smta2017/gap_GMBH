<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use DB;

class RemoveDublicateCompanySeeder extends Seeder
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

            $companies = Company::withTrashed()->where('name' , "LIKE", "%golf globe%")->where('id', '!=', '1')->where('id', '!=', '315')->get();
            
            foreach($companies as $company)
            {
                $company->forceDelete();
            }

            DB::commit();
            
        }catch (\PDOException $e) {
            DB::rollBack();
        }
    }
}
