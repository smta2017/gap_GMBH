<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Region::count() == 0)
        {
            // Region::create([
            //     'id' => '1',
            //     'name' => 'Europe',
            //     'code' => 'eu',
            //     'status' => '1'
            // ]);
            // Region::create([
            //     'id' => '2',
            //     'name' => 'Africa',
            //     'code' => 'Af',
            //     'status' => '1'
            // ]);
            // Region::create([
            //     'id' => '3',
            //     'name' => 'Middle east',
            //     'code' => 'Me',
            //     'status' => '1'
            // ]);
            // Region::create([
            //     'id' => '4',
            //     'name' => 'Asia',
            //     'code' => 'As',
            //     'status' => '1'
            // ]);

            $path = public_path('backend/seeders/regions.sql');
            \DB::unprepared(file_get_contents($path));
        }
    }
}
