<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DestinationFieldType;
use DB;

class DestinationFieldTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(DestinationFieldType::count() == 0)
        {
            $title = DestinationFieldType::create([
                'name' => 'Title',
            ]);
            $title->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Title',    
            ]);
            $title->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Titel',    
            ]);

            $sDes = DestinationFieldType::create([
                'name' => 'Short Description',
            ]);
            $sDes->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Short Description',    
            ]);
            $sDes->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Kurzbeschreibung',    
            ]);

            $des = DestinationFieldType::create([
                'name' => 'Description',
            ]);
            $des->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Description',    
            ]);
            $des->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Beschreibungen',    
            ]);

            $why = DestinationFieldType::create([
                'name' => 'Why',
            ]);
            $why->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Why',    
            ]);
            $why->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Warum',    
            ]);

            $what = DestinationFieldType::create([
                'name' => 'What',
            ]);
            $what->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'What',    
            ]);
            $what->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Was',    
            ]);


        }

        $highCheck = DestinationFieldType::where('name', 'Highlights')->first();

        if(!$highCheck)
        {
            $high = DestinationFieldType::create([
                'name' => 'Highlights',
            ]);
            $high->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Highlights',    
            ]);
            $high->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Highlights',    
            ]);
        }

    }
}
