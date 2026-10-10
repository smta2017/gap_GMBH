<?php

namespace Database\Seeders;

use App\Helper\Helpers;
use App\Models\BasicTranslation;
use Illuminate\Database\Seeder;
use App\Models\FieldType;
use DB;

class FieldTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      
        if(FieldType::count() == 0)
        {
            \DB::select("delete from basic_translations where basicable_type ='App\\\\Models\\\\FieldType'");
            \DB::select('ALTER TABLE field_types AUTO_INCREMENT = 1');

            Helpers::create_field_type('Header', 'Koptekst','golf_course');
            Helpers::create_field_type('Tagline', 'Slogan','golf_course',0);
            Helpers::create_field_type('Short Description', 'Kurzbeschreibung','golf_course');
            Helpers::create_field_type('Golf club description', 'Beschrijving golfclub','golf_course',0);
            Helpers::create_field_type('Characteristics', 'Kenmerken','golf_course');
            Helpers::create_field_type('General info', 'Algemene informatie','golf_course');
            Helpers::create_field_type('Location text', 'Locatie tekst','golf_course');  
       

            Helpers::create_field_type('Header', 'Koptekst','hotel');
            Helpers::create_field_type('Tagline', 'Slogan','hotel',0);
            Helpers::create_field_type('Short Description', 'Kurzbeschreibung','hotel');
            Helpers::create_field_type('Golf club description', 'Beschrijving golfclub','hotel',0);
            Helpers::create_field_type('Characteristics', 'Kenmerken','hotel');
            Helpers::create_field_type('Location text', 'Locatie tekst','hotel');  
        
            
            Helpers::create_field_type('Tagline', 'Slogan','country');
            Helpers::create_field_type('Short Description', 'Kurzbeschreibung','country');
            Helpers::create_field_type('Highlights', 'Highlights','country');
            Helpers::create_field_type('Title', 'Title','country');
        

            Helpers::create_field_type('Tagline', 'Slogan','city');
            Helpers::create_field_type('Short Description', 'Kurzbeschreibung','city');
            Helpers::create_field_type('Highlights', 'Highlights','city');
            Helpers::create_field_type('Title', 'Title','city');
        }
        if(FieldType::whereCategoryId('package')->count() == 0)
        {
            Helpers::create_field_type('Package Details', 'Paket Details','package');  
            Helpers::create_field_type('Golf Details', 'Golf Details','package');  
            Helpers::create_field_type('Hotel Details', 'Hotel Details','package');  
            Helpers::create_field_type('Other', 'Sonstiges','package');  
        }
    }
}
