<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Country::count() == 0)
        {
            // Country::create([
            //     'name' => 'Germany',
            //     'code' => 'DE',
            //     'phone_code' => '+49',
            //     'region_id' => 1,
            //     'language_id' => 1,
            //     'currency_id' => 1,
            //     'status' => '1'
            // ]);
            // Country::create([
            //     'name' => 'Denmark',
            //     'code' => 'DK',
            //     'phone_code' => '+45',
            //     'region_id' => 1,
            //     'language_id' => 1,
            //     'currency_id' => 1,
            //     'status' => '1'
            // ]);
            // Country::create([
            //     'name' => 'Spain',
            //     'code' => 'ES',
            //     'phone_code' => '+34',
            //     'region_id' => 1,
            //     'language_id' => 1,
            //     'currency_id' => 1,
            //     'status' => '1'
            // ]);
            // Country::create([
            //     'name' => 'France',
            //     'code' => 'FR',
            //     'phone_code' => '+33',
            //     'region_id' => 1,
            //     'language_id' => 1,
            //     'currency_id' => 1,
            //     'status' => '1'
            // ]);
            // Country::create([
            //     'name' => 'Angola',
            //     'code' => 'AO',
            //     'phone_code' => '244',
            //     'region_id' => 2,
            //     'language_id' => 1,
            //     'currency_id' => 1,
            //     'status' => '1'
            // ]);
            // Country::create([
            //     'name' => 'Congo',
            //     'code' => 'CD',
            //     'phone_code' => '243',
            //     'region_id' => 2,
            //     'language_id' => 1,
            //     'currency_id' => 1,
            //     'status' => '1'
            // ]);
            // Country::create([
            //     'name' => 'Egypt',
            //     'code' => 'EG',
            //     'phone_code' => '+20',
            //     'region_id' => 3,
            //     'language_id' => 1,
            //     'currency_id' => 1,
            //     'status' => '1'
            // ]);
            // Country::create([
            //     'name' => 'Jordan',
            //     'code' => 'JO',
            //     'phone_code' => '962',
            //     'region_id' => 3,
            //     'language_id' => 1,
            //     'currency_id' => 1,
            //     'status' => '1'
            // ]);

            $path = public_path('backend/seeders/countries.sql');
            \DB::unprepared(file_get_contents($path));
        }
    }
}
