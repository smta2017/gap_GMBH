<?php

namespace Database\Seeders;

use App\Models\BasicTranslation;
use App\Models\Playable;
use Illuminate\Database\Seeder;

class PlayableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        function create_playable($name_en,$name_de,$type)
        {
            $tanss = array(
                new BasicTranslation(array('language_id'=>1 ,'locale'=>'en','name' => $name_en)),
                new BasicTranslation(array('language_id'=>2 ,'locale'=>'de','name' => $name_de)),
            );

            $playable = Playable::create([
                'name'=> $name_en
            ]);    
            $playable->translations()->saveMany($tanss);
        }
        
        if(Playable::count() == 0)
        {
            create_playable('On foot' ,'Zu Fuß','Hotel-General');
            create_playable('With golf cart' ,'Mit Golf Cart','Hotel-General');
        }
    }
}
