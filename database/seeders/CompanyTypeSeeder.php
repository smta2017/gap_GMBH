<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyType;

class CompanyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(CompanyType::count() == 0)
        {
            CompanyType::create([
                'id' => 1,
                'name' => "GG (Golf Globe)"
            ]);
            CompanyType::create([
                'id' => 2,
                'name' => "TA (Travel Agency)"
            ]);
            CompanyType::create([
                'id' => 3,
                'name' => "GC (Golf Club)"
    
            ]);
            CompanyType::create([
                'id' => 4,
                'name' => "HO (Hotel)"
            ]);
            CompanyType::create([
                'id' => 5,
                'name' => "TO (Tour Operator)"
            ]);
            CompanyType::create([
                'id' => 6,
                'name' => "DMC"
            ]);
        }
    }
}
