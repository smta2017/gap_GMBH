<?php

namespace Database\Seeders;

use App\Helper\Helpers;
use App\Models\BasicTranslation;
use Illuminate\Database\Seeder;
use App\Models\Facility;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

       
        if(Facility::where('type','Golf Course')->count() == 0)
        {  
            // Golf Course
            Helpers::create_facility('Halfway Station','Halfway Station'	,'Golf Course' ,'e917'	,'ggicon-Group-1047',	1);
            Helpers::create_facility('Restaurant','Restaurant' ,'Golf Course' ,'e946' ,'ggicon-Path-276', 1);
            Helpers::create_facility('Storage Room','Umkleideräume' ,'Golf Course' ,'e947' ,'ggicon-Path-277', 1);
            Helpers::create_facility('Buggy Bar','Buggy-Bar' ,'Golf Course' ,'e918' ,'ggicon-Group-1048', 1);
            Helpers::create_facility('Pro Shop','Pro Shop' ,'Golf Course' ,'e948' ,'ggicon-Path-279', 1);
            Helpers::create_facility('Parking','Parkplatz' ,'Golf Course' ,'e949' ,'ggicon-Path-280', 1);
            Helpers::create_facility('Conference Room','Konferenzräum' ,'Golf Course' ,'e94a' ,'ggicon-Path-281', 1);
            Helpers::create_facility('Shower','Duschen' ,'Golf Course' ,'e94b' ,'ggicon-Path-282', 1);
            Helpers::create_facility('Sauna','Sauna' ,'Golf Course' ,'e91a' ,'ggicon-Group-1049', 1);
            Helpers::create_facility('Gym','Fitnessraum' ,'Golf Course' ,'e94c' ,'ggicon-Path-283', 1);
            Helpers::create_facility('Pools','Pool' ,'Golf Course' ,'e94d'	,'ggicon-Path-284',	1);
            Helpers::create_facility('Club House','Club House' ,'Golf Course' ,'' ,'', 1);
            Helpers::create_facility('Snack Bar','Snack Bar' ,'Golf Course' ,'' ,'', 1);
    
        }


        if(Facility::where('type','Hotel')->count() == 0)
        {        

            // Hotels
            Helpers::create_facility("Parking lots available","Parkplätze vorhanden","Hotel");
            Helpers::create_facility("Changing room","Umkleideraum","Hotel");
            Helpers::create_facility("Mini market","Minimarkt","Hotel");
            Helpers::create_facility("Boutique","Boutique","Hotel");
            Helpers::create_facility("Pools","Pools","Hotel");
            Helpers::create_facility("Fitness room / center","Fitnessraum/-center","Hotel");
            Helpers::create_facility("Wellness area","Wellnessbereich","Hotel");
            Helpers::create_facility("Hotel's own golf course","HOTELEIGENER GOLFPLATZ","Hotel");
            Helpers::create_facility("Hammam","Hammam","Hotel");
            Helpers::create_facility("Surf school","Surfschule","Hotel");
            Helpers::create_facility("Beauty / cosmetics center","Beauty-/Kosmetikcenter","Hotel");
            Helpers::create_facility("Mini Golf","Minigolf","Hotel");
            Helpers::create_facility("Community pool","Gemeinschaftspool","Hotel");
            Helpers::create_facility("PADI diving school","PADI Tauchschule","Hotel");
            Helpers::create_facility("GYM","GYM","Hotel");
            Helpers::create_facility("Jacuzzi","Jacuzzi","Hotel");
            Helpers::create_facility("SPA","SPA","Hotel");
            Helpers::create_facility("shower","shower","Hotel");
             

            }
    }
}
