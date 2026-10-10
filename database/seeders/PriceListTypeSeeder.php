<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PriceListType;

class PriceListTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(PriceListType::count() == 0)
        {        
            PriceListType::create([
                'name' => 'Selling',
            ]);
            PriceListType::create([
                'name' => 'Purchasing',
            ]);
            PriceListType::create([
                'name' => 'Tui',
            ]);
        }
    }
}
