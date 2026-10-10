<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EnquiryStatus;

class EnquiryStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(EnquiryStatus::count() == 0)
        {
            EnquiryStatus::create([
                'name' => 'New',
            ]);
            EnquiryStatus::create([
                'name' => 'Delayed',
            ]);
            EnquiryStatus::create([
                'name' => 'In Progress',
            ]);
            EnquiryStatus::create([
                'name' => 'Qualified',
            ]);
            EnquiryStatus::create([
                'name' => 'Canceled',
            ]);
        }
    }
}
