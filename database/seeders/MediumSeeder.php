<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medium;

class MediumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Medium::count() == 0)
        {
            Medium::create([
                'name' => 'GG',
            ]);
            Medium::create([
                'name' => 'Facebook',
            ]);
            Medium::create([
                'name' => 'Walk in',
            ]);
            Medium::create([
                'name' => 'Call',
            ]);
            Medium::create([
                'name' => 'WhatsApp',
            ]);
            Medium::create([
                'name' => 'Goolgle',
            ]);
            Medium::create([
                'name' => 'Bing',
            ]);
            Medium::create([
                'name' => 'LinkedIn',
            ]);
        }
    }
}
