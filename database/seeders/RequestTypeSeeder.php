<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequestType;
use DB;

class RequestTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(RequestType::count() == 0)
        {
            RequestType::create([
                'name' => 'Golf',
            ]);
            RequestType::create([
                'name' => 'Hotel',
                'status' => '0'
            ]);
            RequestType::create([
                'name' => 'Golf Holiday',
                'status' => '0'
            ]);
        }
    }
}
