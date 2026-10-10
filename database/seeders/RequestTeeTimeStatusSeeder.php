<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequestTeeTimeStatus;

class RequestTeeTimeStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(RequestTeeTimeStatus::count() == 0)
        {
            RequestTeeTimeStatus::create([
                'name' => 'new'
            ]);
            RequestTeeTimeStatus::create([
                'name' => 'Redirected'
            ]);
            RequestTeeTimeStatus::create([
                'name' => 'Rejected'
            ]);
            RequestTeeTimeStatus::create([
                'name' => 'Confirmed'
            ]);
        }

        if(!RequestTeeTimeStatus::find(5))
        {
            RequestTeeTimeStatus::create([
                'name' => 'Canceled'
            ]);
        }
        if(!RequestTeeTimeStatus::find(6))
        {
            RequestTeeTimeStatus::create([
                'name' => 'Approved'
            ]);
        }
    }
}
