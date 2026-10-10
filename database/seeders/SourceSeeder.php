<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Source;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Source::count() == 0)
        {
            Source::create([
                'name' => 'Website',
            ]);
            Source::create([
                'name' => 'Search',
            ]);
            Source::create([
                'name' => 'Social',
            ]);
            Source::create([
                'name' => 'Referral',
            ]);
            Source::create([
                'name' => 'Organic',
            ]);
        }
    }
}
