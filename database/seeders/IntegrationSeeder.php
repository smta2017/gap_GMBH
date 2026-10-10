<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Integration;
use App\Models\Company;

class IntegrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Integration::count() == 0)
        {
            // $apiKey = \Str::random(64);
            $apiKey = "9n7zWQT1ZzGh6T3rBg4JneUKZGvNXUWM6oPDZzuFAna7I7YWjTtVtoAFZwgBj7dC";

            $company = Company::where('company_type_id', '1')->first();

            if($company)
            {
                $integration = Integration::create([
                    'name' => 'Wessam Mohsen API KEY',
                    'description' => 'Wessam Mohsen - Main Golf Globe website integration',
                    'api_key' => $apiKey,
                    'status' => '1',
                    'expiry_date' => '2022-10-28',

                    'company_id' => $company->id,
                ]);
            }
        }
    }
}
