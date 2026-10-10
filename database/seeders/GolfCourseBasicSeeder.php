<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Difficulty;
use App\Models\Terrain;
use App\Models\DressCode;

class GolfCourseBasicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Difficulty::count() == 0)
        {
            $easy = Difficulty::create([
                'name' => 'Easy',
                'status' => '1'
            ]);

            $easy->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Easy',    
            ]);

            $easy->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'leicht',    
            ]);


            $sporty = Difficulty::create([
                'name' => 'Sporty',
                'status' => '1'
            ]);

            $sporty->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Sporty',    
            ]);

            $sporty->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Sportlich',    
            ]);

            $ch = Difficulty::create([
                'name' => 'Challenging',
                'status' => '1'
            ]);

            $ch->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Challenging',    
            ]);

            $ch->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Herausfordernd',    
            ]);

            $mo = Difficulty::create([
                'name' => 'Moderate',
                'status' => '1'
            ]);

            $mo->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Moderate',    
            ]);

            $mo->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Mäßig',    
            ]);

            $hard = Difficulty::create([
                'name' => 'Hard',
                'status' => '1'
            ]);

            $hard->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Hard',    
            ]);

            $hard->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Hart',    
            ]);

            $de = Difficulty::create([
                'name' => 'Demanding',
                'status' => '1'
            ]);

            $de->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Demanding',    
            ]);

            $de->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Anspruchsvolle',    
            ]);

            $pl = Difficulty::create([
                'name' => 'Plenty of strategic options',
                'status' => '1'
            ]);

            $pl->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Plenty of strategic options',    
            ]);

            $pl->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Vielfältige strategische Optionen',    
            ]);

        }

        if(Terrain::count() == 0)
        {
            $flat = Terrain::create([
                'name' => 'Flat',
                'status' => '1'
            ]);
            $flat->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Flat',    
            ]);

            $flat->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'flaches',    
            ]);


            $hi = Terrain::create([
                'name' => 'Hilly',
                'status' => '1'
            ]);

            $hi->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Hilly',    
            ]);

            $hi->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'hügelig',    
            ]);

            $vHi = Terrain::create([
                'name' => 'Very Hilly',
                'status' => '1'
            ]);
            $vHi->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Very Hilly',    
            ]);

            $vHi->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'sehr hügelig',    
            ]);

            $sHi = Terrain::create([
                'name' => 'Slightly Hilly',
                'status' => '1'
            ]);

            $sHi->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Slightly Hilly',    
            ]);

            $sHi->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'leicht hügelig',    
            ]);

            $water = Terrain::create([
                'name' => 'Water Hazard',
                'status' => '1'
            ]);
            $water->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Water Hazard',    
            ]);

            $water->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Wasserhindernis',    
            ]);
            
            $un = Terrain::create([
                'name' => 'Undulating',
                'status' => '1'
            ]);

            $un->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Undulating',    
            ]);

            $un->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'wellenförmig',    
            ]);

            $sUn = Terrain::create([
                'name' => 'slightly undulating',
                'status' => '1'
            ]);

            $sUn->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'slightly undulating',    
            ]);

            $sUn->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'leicht hügelig',    
            ]);

            $h = Terrain::create([
                'name' => 'Hybrid Bermuda grass',
                'status' => '1'
            ]);

            $h->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Hybrid Bermuda grass',    
            ]);

            $h->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Hybrid-Bermuda-Gras',    
            ]);

        }

        if(DressCode::count() == 0)
        {
            $soft = DressCode::create([
                'name' => 'Soft Spikes',
                'status' => '1'
            ]);
            $soft->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Soft Spikes',    
            ]);

            $soft->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Weiche Spikes',    
            ]);

            $softS = DressCode::create([
                'name' => 'Soft Spikes recommended',
                'status' => '1'
            ]);
            $softS->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Soft Spikes recommended',    
            ]);

            $softS->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Soft Spikes empfohlen',    
            ]);

            $je = DressCode::create([
                'name' => 'no jeans',
                'status' => '1'
            ]);

            $je->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'no jeans',    
            ]);

            $je->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Keine Jeans',    
            ]);

            $te = DressCode::create([
                'name' => 'no T-Shirt',
                'status' => '1'
            ]);

            $te->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'no T-Shirt',    
            ]);

            $te->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Kein T-Shirt',    
            ]);

        }
    }
}
