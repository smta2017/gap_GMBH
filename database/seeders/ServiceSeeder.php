<?php

namespace Database\Seeders;

use App\Helper\Helpers;
use App\Models\BasicTranslation;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceProperty;
use App\Models\ServiceAddon;
use App\Models\ServiceFeeDetails;
use App\Models\ClubBrand;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        
        if(ClubBrand::count() == 0)
        {
            ClubBrand::create([
                'name' => 'Wilson',
                'status' => '1'
            ]);
            ClubBrand::create([
                'name' => 'Cleavland',
                'status' => '1'
            ]);
            ClubBrand::create([
                'name' => 'Taylor Made',
                'status' => '1'
            ]);
            ClubBrand::create([
                'name' => 'basepremium',
                'status' => '1'
            ]);
            ClubBrand::create([
                'name' => 'TaylorMade (Gentleman)',
                'status' => '1'
            ]);
            ClubBrand::create([
                'name' => 'Cleveland (Ladies)',
                'status' => '1'
            ]);
            ClubBrand::create([
                'name' => 'Callaway',
                'status' => '1'
            ]);
            ClubBrand::create([
                'name' => 'T-flite',
                'status' => '1'
            ]);
            ClubBrand::create([
                'name' => 'Srixon',
                'status' => '1'
            ]);
            ClubBrand::create([
                'name' => 'King Cobra Grafite',
                'status' => '1'
            ]);
            ClubBrand::create([
                'name' => 'Basic',
                'status' => '1'
            ]);
            ClubBrand::create([
                'name' => 'High Quality',
                'status' => '1'
            ]);
            ClubBrand::create([
                'name' => 'Standard',
                'status' => '1'
            ]);
            ClubBrand::create([
                'name' => 'Premium',
                'status' => '1'
            ]);
            ClubBrand::create([
                'name' => 'Cobra',
                'status' => '1'
            ]);
            ClubBrand::create([
                'name' => 'Full Set',
                'status' => '1'
            ]);
        }
        
        if(Service::count() == 0)
        {

            // GPS
            $gps = Service::create([
                'name' => 'gps',
                'type' => 'Golf Course',
                'view_type' => 'boolean',
            ]);

            $gps->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'GPS', 
            ]);

            $gps->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'GPS',    
            ]);

            // Trolly
            $trolly = Service::create([
                'name' => 'Trolly',
                'type' => 'Golf Course',
                'view_type' => 'boolean',
            ]);

            $trolly->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Trolly', 
            ]);

            $trolly->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Trolley',    
            ]);

            ServiceProperty::create([
                'service_id' => $trolly->id,
                'name' => 'Trolly Reservation',

                'view_type' => 'select',
                'options' => 'Yes, No, In Advance, Unknown'
            ]);

            // Buggy
            $buggy = Service::create([
                'name' => 'Buggy',
                'type' => 'Golf Course',
                'view_type' => 'boolean',
            ]);

            $buggy->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Buggy', 
            ]);

            $buggy->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Buggy',    
            ]);

            ServiceProperty::create([
                'service_id' => $buggy->id,
                'name' => 'Buggy Reservation',

                'view_type' => 'select',
                'options' => 'Yes, No, In Advance, Unknown'
            ]);
            ServiceProperty::create([
                'service_id' => $buggy->id,
                'name' => 'Buggy Mandatory',

                'view_type' => 'select',
                'options' => 'Yes, No, In Advance, Unknown'
            ]);
            ServiceProperty::create([
                'service_id' => $buggy->id,
                'name' => 'Buggy Number',

                'view_type' => 'number',
            ]);
            ServiceProperty::create([
                'service_id' => $buggy->id,
                'name' => 'Buggy Note',

                'view_type' => 'textarea',
            ]);

            ServiceFeeDetails::create([
                'service_id' => $buggy->id,

                'unit_type' => 'list',
                'unit_options' => '1x18, 3x18, 5x18',
            ]);

            // E Trolly
            $eTrolly = Service::create([
                'name' => 'E-Trolly',
                'type' => 'Golf Course',
                'view_type' => 'boolean',
            ]);

            $eTrolly->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'E-Trolly', 
            ]);

            $eTrolly->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'E-Trolly',    
            ]);

            ServiceProperty::create([
                'service_id' => $eTrolly->id,
                'name' => 'E-Trolly Numbers',

                'view_type' => 'number',
            ]);
            
            // Manual Trolly
            $mTrolly = Service::create([
                'name' => 'Manual Trolly',
                'type' => 'Golf Course',
                'view_type' => 'boolean',
            ]);
            $mTrolly->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Manual Trolly', 
            ]);

            $mTrolly->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Manueller Trolley',    
            ]);

            ServiceProperty::create([
                'service_id' => $mTrolly->id,
                'name' => 'Manual Trolly Numbers',

                'view_type' => 'number',
            ]);

            // Driving Range
            $dRange = Service::create([
                'name' => 'Driving Range',
                'type' => 'Golf Course',
                'view_type' => 'boolean',
            ]);
            $dRange->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Driving Range', 
            ]);

            $dRange->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Driving Range',    
            ]);

            $rangeball = ServiceAddon::create([
                'service_id' => $dRange->id,
                'name' => 'Range Ball',
                'type' => 'Golf Course',
                
                'view_type' => 'boolean',
            ]);

            ServiceFeeDetails::create([
                'service_id' => $dRange->id,
                'addon_id' => $rangeball->id,
                'unit_type' => 'list',
                'unit_options' => 'basket, half basket, unlimited',
            ]);

            // Rental Club
            $rClub = Service::create([
                'name' => 'Rental Club',
                'type' => 'Golf Course',
                'view_type' => 'select',
                'options' => 'Yes, No, In Advance, Unknown',
            ]);
            $rClub->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Rental Club', 
            ]);

            $rClub->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Leihschläger',    
            ]);

            ServiceProperty::create([
                'service_id' => $rClub->id,
                'name' => 'Rentals Clubs Brand',

                'view_type' => 'multi-select',
                'options' => 'Wilson, Cleavland, Taylor Made, base premium, TaylorMade (Gentleman), Cleveland (Ladies), Callaway, T-flite, Srixon, King Cobra Grafite, Basic, High Quality, Standard, Premium, Cobra, Full Set'
            ]);

            ServiceFeeDetails::create([
                'service_id' => $rClub->id,
        
                'unit_type' => 'list',
                'unit_options' => 'Week, 9 Holes, 18 Holes',
            ]);

            // Locker
            $locker = Service::create([
                'name' => 'Locker',
                'type' => 'Golf Course',
                'view_type' => 'boolean',
            ]);
            $locker->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Locker', 
            ]);

            $locker->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Schließfach',    
            ]);

            // Storage Room
            $sRoom = Service::create([
                'name' => 'Storage Room',
                'type' => 'Golf Course',
                'view_type' => 'boolean',
            ]);
            $sRoom->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Storage Room', 
            ]);

            $sRoom->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Abstellraum',    
            ]);

            // Caddy
            $caddy = Service::create([
                'name' => 'Caddy',
                'type' => 'Golf Course',
                'view_type' => 'select',
                'options' => 'Yes, No, In Advance, Unknown',
            ]);
            $caddy->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Caddy', 
            ]);

            $caddy->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Caddy',    
            ]);

            // cWater
            $cWater = Service::create([
                'name' => 'Complementary Water',
                'type' => 'Golf Course',
                'view_type' => 'select',
                'options' => '1 small, Dispensers at holes, No, Included with the Buggy'
            ]);
            $cWater->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Complementary Water', 
            ]);

            $cWater->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Komplementäres Wasser',    
            ]);

            ServiceProperty::create([
                'service_id' => $cWater->id,
                'name' => 'Complementary Club Cleaning',

                'view_type' => 'select',
                'options' => 'Yes, No, Hotel Guests only'
            ]);


            // Halfway station
            $hStation = Service::create([
                'name' => 'Halfway station',
                'type' => 'Golf Course',
                'view_type' => 'boolean',
            ]);
            $hStation->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Halfway station', 
            ]);

            $hStation->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Station auf halber Strecke',    
            ]);

            // Buggy Bar
            $bBar = Service::create([
                'name' => 'Buggy Bar',
                'type' => 'Golf Course',
                'view_type' => 'boolean',
            ]);
            $bBar->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Buggy Bar', 
            ]);

            $bBar->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Buggy Bar',    
            ]);

            // Launch Package
            $lPackage = Service::create([
                'name' => 'Launch Package',
                'type' => 'Golf Course',
                'view_type' => 'boolean',
            ]);
            $lPackage->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Launch Package', 
            ]);

            $lPackage->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Einführungspaket',    
            ]);

            // Complementary transfer
            $ComplementaryTransfer = Service::create([
                'name' => 'Complementary transfer',
                'type' => 'Golf Course',
                'view_type' => 'boolean',
            ]);
            $ComplementaryTransfer->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Complementary transfer', 
            ]);

            $ComplementaryTransfer->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Komplementäre Übertragung',    
            ]);

            /**
             * 
             *      Training Services
             * 
            */


            // Driving Range
            $tDrivingRange = Service::create([
                'name' => 'Driving Range',
                'type' => 'Training',
                'view_type' => 'boolean',
            ]);
            $tDrivingRange->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Driving Range', 
            ]);

            $tDrivingRange->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Driving Range',    
            ]);

            ServiceProperty::create([
                'service_id' => $tDrivingRange->id,
                'name' => 'Covered Driving Range',
                'view_type' => 'boolean'
            ]);

            ServiceProperty::create([
                'service_id' => $tDrivingRange->id,
                'name' => 'Two Stories',
                'view_type' => 'boolean'
            ]);

            // Practice Mats
            $tPracticeMats = Service::create([
                'name' => 'Practice Mats',
                'type' => 'Training',
                'view_type' => 'boolean',
            ]);
            $tPracticeMats->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Practice Mats', 
            ]);

            $tPracticeMats->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Übungsmatten',    
            ]);

            ServiceProperty::create([
                'service_id' => $tPracticeMats->id,
                'name' => 'Covered mats',
                'view_type' => 'boolean'
            ]);

            // Chipping/Pitching Green
            $tCh = Service::create([
                'name' => 'Chipping/Pitching Green',
                'type' => 'Training',
                'view_type' => 'boolean',
            ]);

            $tCh->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Chipping/Pitching Green', 
            ]);

            $tCh->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Chipping/Pitching Green',    
            ]);

            // Putting Green
            $rPutting = Service::create([
                'name' => 'Putting Green',
                'type' => 'Training',
                'view_type' => 'boolean',
            ]);
            $rPutting->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Putting Green', 
            ]);

            $rPutting->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Putting Green',    
            ]);

            // Bunker
            $tBunker = Service::create([
                'name' => 'Bunker',
                'type' => 'Training',
                'view_type' => 'boolean',
            ]);
            $tBunker->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Bunker', 
            ]);

            $tBunker->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Bunker',    
            ]);

            // Practice
            $tPractice = Service::create([
                'name' => 'Practice',
                'type' => 'Training',
                'view_type' => 'boolean',
            ]);
            $tPractice->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Practice', 
            ]);

            $tPractice->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Übung',    
            ]);

            // Lessons
            $tlessons = Service::create([
                'name' => 'Lessons',
                'type' => 'Training',
                'view_type' => 'boolean',
            ]);
            $tlessons->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Lessons', 
            ]);

            $tlessons->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Lektionen',    
            ]);

            // Balls
            $tBalls = Service::create([
                'name' => 'Balls',
                'type' => 'Training',
                'view_type' => 'boolean',
            ]);
            $tBalls->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Balls', 
            ]);

            $tBalls->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Bälle',    
            ]);

            // Practice Balls
            $tPBalls = Service::create([
                'name' => 'Practice Balls',
                'type' => 'Training',
                'view_type' => 'boolean',
            ]);
            $tPBalls->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Practice Balls', 
            ]);

            $tPBalls->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Übungsbälle',    
            ]);

            // unlimited Practice balls
            $tunlimitedPBall = Service::create([
                'name' => 'unlimited Practice balls',
                'type' => 'Training',
                'view_type' => 'boolean',
            ]);
            $tunlimitedPBall->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'unlimited Practice balls', 
            ]);

            $tunlimitedPBall->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Unbegrenzt Übungsbälle',    
            ]);

            ServiceFeeDetails::create([
                'service_id' => $tunlimitedPBall->id,

                'unit_type' => 'list',
                'unit_options' => 'Hpur, Day, Week',
            ]);

            // Caddy Master
            $tCaddyMaster = Service::create([
                'name' => 'Caddy Master',
                'type' => 'Training',
                'view_type' => 'boolean',
            ]);

            $tCaddyMaster->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Caddy Master', 
            ]);

            $tCaddyMaster->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Caddy-Master',    
            ]);


            // Short Course
            $tCaddyMaster = Service::create([
                'name' => 'Short Course',
                'type' => 'Training',
                'view_type' => 'boolean',
            ]);

            $tCaddyMaster->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Short Course', 
            ]);

            $tCaddyMaster->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Kurzplatz',    
            ]);
            

        }

        /**
         * 
         *      Hotel Services
         * 
        */
        // remove after deploy
        // Service::where('type', 'Hotel-General')->forceDelete();
        // Service::where('type', 'Hotel-Sport')->forceDelete();
        // Service::where('type', 'Hotel-Golf')->forceDelete();
        // Service::where('type', 'Hotel-Entertainment')->forceDelete();
        // Service::where('type', 'Hotel-Surroundings')->forceDelete();
        // \DB::statement("ALTER TABLE `services` AUTO_INCREMENT = 1;");


        if(Service::where('type', 'Hotel-General')->count() == 0)
        {
            Helpers::create_service('Private Parking' ,'Privater Parkplatz','Hotel-General');
            Helpers::create_service('Garage' ,'Garage','Hotel-General');
            Helpers::create_service('Valet Parking' ,'Parkservice','Hotel-General');
            Helpers::create_service('Conference Room' ,'Konferenz- und Veranstaltungsräume','Hotel-General');
            Helpers::create_service('Free WiFi' ,'Kostenloses WLAN','Hotel-General');
            Helpers::create_service('Sun Terrace' ,'Sonnenterrasse','Hotel-General');
            Helpers::create_service('Sun Loungers' ,'Sonnenliegen','Hotel-General');
            Helpers::create_service('Garden' ,'Garten','Hotel-General');
            Helpers::create_service('Concierge Service' ,'Conciergeservice','Hotel-General');
            Helpers::create_service('Shops' ,'Shops','Hotel-General');
            Helpers::create_service('Laundry service' ,'Wäscheservice','Hotel-General');
            Helpers::create_service('Entertainment' ,'Animation','Hotel-General');
            Helpers::create_service('Lounge' ,'Lounge','Hotel-General');
            Helpers::create_service('Charging station for electric vehicles' ,'Ladestation für Elektrofahrzeuge','Hotel-General');
            Helpers::create_service('Luggage store' ,'Gepäckaufbewahrung','Hotel-General');
            Helpers::create_service('Beach Private Area' ,'Privatstrand','Hotel-General');
            Helpers::create_service('Beach access' ,'direkter Strandzugang','Hotel-General');
            Helpers::create_service('Pet friendly hotel' ,'haustierfreundliches Hotel','Hotel-General');
            
        }


        if(Service::where('type', 'Hotel-Sport&Wellness')->count() == 0)
        { 
            Helpers::create_service('Hotel with own golf course','hoteleigener Golfplatz','Hotel-Sport&Wellness');
            Helpers::create_service('Tennis Court','Tennisplatz','Hotel-Sport&Wellness');
            Helpers::create_service('Water Sports','Wassersport','Hotel-Sport&Wellness');
            Helpers::create_service('Bike Rental','Fahrradverleih','Hotel-Sport&Wellness');
            Helpers::create_service('Fitness center','Fitnessraum','Hotel-Sport&Wellness');
            Helpers::create_service('Yoga','Yoga','Hotel-Sport&Wellness');
            Helpers::create_service('Sports Facilities','Weitere Sportanlagen','Hotel-Sport&Wellness');
            Helpers::create_service('Wellness area','Wellnessbereich','Hotel-Sport&Wellness');
            Helpers::create_service('Sauna','Sauna','Hotel-Sport&Wellness');
            Helpers::create_service('Steam bath','Dampfbad','Hotel-Sport&Wellness');
            Helpers::create_service('Hammam','Hammam','Hotel-Sport&Wellness');
            Helpers::create_service('Hairdressing/Beauty Saloon','Friseur-/Schönheitssalon','Hotel-Sport&Wellness');
            Helpers::create_service('Massage','Massage','Hotel-Sport&Wellness');
            Helpers::create_service('Thermal bath','Thermalbad','Hotel-Sport&Wellness');
            Helpers::create_service('Whirlpool','Whirlpool','Hotel-Sport&Wellness');
            Helpers::create_service('Relaxation room','Ruhebereich','Hotel-Sport&Wellness');
            Helpers::create_service('Indoor Pool','Innenpool','Hotel-Sport&Wellness');
            Helpers::create_service('Heated Indoor Pool','Beheizter Innenpool','Hotel-Sport&Wellness');
            Helpers::create_service('Outdoor Pool','Außenpool','Hotel-Sport&Wellness');
            Helpers::create_service('Heated Outdoor Pool','Beheizter Außenpool','Hotel-Sport&Wellness');

        }
        

        if(Service::where('type', 'Hotel-Food&Drinks')->count() == 0)
        {
            Helpers::create_service('Restaurant','Restaurant','Hotel-Food&Drinks');
            Helpers::create_service('Buffet Restaurant','Buffetrestaurant','Hotel-Food&Drinks');
            Helpers::create_service('À la carte Restaurant','À la carte Restaurant','Hotel-Food&Drinks');
            Helpers::create_service('Theme Restaurant','Themenrestaurant','Hotel-Food&Drinks');
            Helpers::create_service('Bar','Bar','Hotel-Food&Drinks');
            Helpers::create_service('Pool Bar','Pool Bar','Hotel-Food&Drinks');
            Helpers::create_service('Beach Club','Beach Club','Hotel-Food&Drinks');
            Helpers::create_service('Piano Bar','Piano Bar','Hotel-Food&Drinks');
        }
         
        if(Service::where('type', 'Hotel-Golf&Highlights')->count() == 0)
        {
            Helpers::create_service('At golf course','Am Golfplatz','Hotel-Golf&Highlights','e94e','ggicon-Path-288');
            Helpers::create_service('Luxury resort','Luxuriöses Resort','Hotel-Golf&Highlights','e925','ggicon-Group-1069');
            Helpers::create_service('Sustainable travel','Nachhaltiges Reisen','Hotel-Golf&Highlights','e926','ggicon-Group-1070');
            Helpers::create_service('Adults Only Hotel','Nur-Erwachsenen-Hotel','Hotel-Golf&Highlights','e924','ggicon-Group-10761');
            Helpers::create_service('All-Inclusive Hotel','All-Inclusive-Hotel','Hotel-Golf&Highlights');
            Helpers::create_service('Recommended for groups','Empfohlen für Gruppen','Hotel-Golf&Highlights','e919',	'ggicon-Group-1017');
            Helpers::create_service('Golf Globe Top Partner','Golf Globe Top Partner','Hotel-Golf&Highlights','e944', 'ggicon-Path-274');
            Helpers::create_service('Golf Desk','Golf Desk','Hotel-Golf&Highlights');
            Helpers::create_service('Golf shuttle','Golf shuttle','Hotel-Golf&Highlights');
        }

        Service::where('name', '$gps')->update([
            'name' => 'gps'
        ]);
    }
}
