<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hole;

class HolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Hole::count() == 0)
        {
            Hole::create([
                'name' => '9',
            ]);
            Hole::create([
                'name' => '18',
            ]);
            Hole::create([
                'name' => '27',
            ]);
            Hole::create([
                'name' => '36',
            ]);
            Hole::create([
                'name' => 'Twilight',
            ]);
        }
    }
}
