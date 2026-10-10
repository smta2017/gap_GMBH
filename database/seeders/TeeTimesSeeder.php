<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeeTime;

class TeeTimesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(TeeTime::count() == 0)
        {
            TeeTime::create([
                'name' => '0',
            ]);
            TeeTime::create([
                'name' => '1',
            ]);
            TeeTime::create([
                'name' => '2',
            ]);
            TeeTime::create([
                'name' => '3',
            ]);
            TeeTime::create([
                'name' => '4',
            ]);
            TeeTime::create([
                'name' => '5',
            ]);
            TeeTime::create([
                'name' => '6',
            ]);
            TeeTime::create([
                'name' => '7',
            ]);
            TeeTime::create([
                'name' => '8',
            ]);
            TeeTime::create([
                'name' => '9',
            ]);
            TeeTime::create([
                'name' => '10',
            ]);
            TeeTime::create([
                'name' => '11',
            ]);
            TeeTime::create([
                'name' => '12',
            ]);
        }
    }
}
