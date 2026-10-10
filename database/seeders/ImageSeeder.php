<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Image;
use DB;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Image::count() == 0)
        {
            $path = public_path('backend/seeders/images.sql');
            \DB::unprepared(file_get_contents($path));
        }
    }
}
