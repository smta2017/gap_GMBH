<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $englishCheck = Language::find('1');

        if(!$englishCheck)
        {
            Language::create([
                'name' => 'English',
                'code' => 'en'
            ]);
        }

        $deutschCheck = Language::find('2');

        if(!$deutschCheck)
        {
            Language::create([
                'name' => 'Deutsch',
                'code' => 'de'
            ]);
        }

    }
}
