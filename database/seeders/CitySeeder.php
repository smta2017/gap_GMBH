<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::where('name', 'all cities')->delete();

        City::where('name', 'rl_test_region')->delete();
        City::where('name', 'jvtest')->delete();
        
        if(City::count() == 0)
        {
            // City::create([
            //     'name' => 'Berlin',
            //     'code' => 'BER',
            //     'region_id' => 1,
            //     'country_id' => 1,
            //     'language_id' => 1,
            //     'status' => '1'
            // ]);
            // City::create([
            //     'name' => 'Hamburg',
            //     'code' => 'HAM',
            //     'region_id' => 1,
            //     'country_id' => 1,
            //     'language_id' => 1,
            //     'status' => '1'
            // ]);
            // City::create([
            //     'name' => 'Munich (München)',
            //     'code' => 'MUC',
            //     'region_id' => 1,
            //     'country_id' => 1,
            //     'language_id' => 1,
            //     'status' => '1'
            // ]);
            $path = public_path('backend/seeders/cities.sql');
            \DB::unprepared(file_get_contents($path));
        }
    }
}
