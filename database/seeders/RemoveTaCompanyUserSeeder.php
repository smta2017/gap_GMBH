<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\TravelAgency;

class RemoveTaCompanyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::where('company_type_id', '2')->get();

        foreach($companies as $company)
        {
            $taCheck = TravelAgency::where('company_id', $company->id)->first();

            if(!$taCheck)
            {
                $users = User::whereHas('details', function($q) use ($company){
                    $q->where('company_id', $company->id);
                })->get();

                foreach($users as $user)
                {
                    $user->forceDelete();
                }

                $company->forceDelete();
            }
        }
    }
}
