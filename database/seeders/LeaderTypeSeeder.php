<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaderType;
use DB;

class LeaderTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(LeaderType::count() == 0)
        {
            LeaderType::create([
                'name' => 'TA Leader',
            ]);
            LeaderType::create([
                'name' => 'Group Leader',
            ]);
            LeaderType::create([
                'name' => 'Pro Leader',
                'has_hcp' => '0'
            ]);
            LeaderType::create([
                'name' => 'Golf President',
                'has_company' => '1'
            ]);
        }
    }
}
