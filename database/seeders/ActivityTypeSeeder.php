<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityType;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(ActivityType::count() == 0)
        {        
            ActivityType::create([
                'name' => 'Maintenance',
                'status' => '1',
            ]);
            ActivityType::create([
                'name' => 'Overseeding',
                'status' => '1',
            ]);
            ActivityType::create([
                'name' => 'Tee Time Reservation',
                'status' => '1',
            ]);
            ActivityType::create([
                'name' => 'Tournament',
                'status' => '1',
            ]);
        }
    }
}
