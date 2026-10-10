<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequestProductStatus;

class RequestProductStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(RequestProductStatus::count() == 0)
        {
            RequestProductStatus::create([
                'name' => 'new'
            ]);
            RequestProductStatus::create([
                'name' => 'Redirected'
            ]);
            RequestProductStatus::create([
                'name' => 'Rejected'
            ]);
            RequestProductStatus::create([
                'name' => 'Confirmed'
            ]);
        }

        if(!RequestProductStatus::find(5))
        {
            RequestProductStatus::create([
                'name' => 'Canceled'
            ]);
        }
        if(!RequestProductStatus::find(6))
        {
            RequestProductStatus::create([
                'name' => 'Approved'
            ]);
        }
    }
}
