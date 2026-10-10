<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(RoomType::count() == 0)
        {
            $_1 = RoomType::create([
                'name' => 'Apartment',
                'status' => '1'
            ]);

            $_1->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Apartment',    
            ]);

            $_1->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Appartemnet',    
            ]);

            $_2 = RoomType::create([
                'name' => 'Double room for single use',
                'status' => '1'
            ]);

            $_2->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Double room for single use',    
            ]);

            $_2->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Doppelzimmer Zur Alleinbenutzung',    
            ]);

            $_3 = RoomType::create([
                'name' => 'Double room',
                'status' => '1'
            ]);

            $_3->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Double room',    
            ]);

            $_3->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Doppelzimmer',    
            ]);

            $_4 = RoomType::create([
                'name' => 'Junior suite',
                'status' => '1'
            ]);

            $_4->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Junior suite',    
            ]);

            $_4->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Junior suite',    
            ]);

            $_5 = RoomType::create([
                'name' => 'Deluxe suite',
                'status' => '1'
            ]);

            $_5->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Deluxe suite',    
            ]);

            $_5->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Deluxe suite',    
            ]);

            $_6 = RoomType::create([
                'name' => 'suite',
                'status' => '1'
            ]);

            $_6->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'suite',    
            ]);

            $_6->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'suite',    
            ]);

            $_7 = RoomType::create([
                'name' => 'Double room comfort class',
                'status' => '1'
            ]);

            $_7->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Double room comfort class',    
            ]);

            $_7->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Doppelzimmer Komfortklasse',    
            ]);

            $_8 = RoomType::create([
                'name' => 'Deluxe double room',
                'status' => '1'
            ]);

            $_8->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Deluxe double room',    
            ]);

            $_8->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Deluxe-Doppelzimmer',    
            ]);

            $_9 = RoomType::create([
                'name' => 'Double room economy',
                'status' => '1'
            ]);

            $_9->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Double room economy',    
            ]);

            $_9->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Doppelzimmer Economy',    
            ]);

            $_10 = RoomType::create([
                'name' => 'Superior double room',
                'status' => '1'
            ]);

            $_10->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Superior double room',    
            ]);

            $_10->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Superior-Doppelzimmer',    
            ]);

            $_11 = RoomType::create([
                'name' => 'Superior deluxe double room',
                'status' => '1'
            ]);

            $_11->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Superior deluxe double room',    
            ]);

            $_11->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Superior -deluxe-Doppelzimmer',    
            ]);

            $_12 = RoomType::create([
                'name' => 'Classic double room',
                'status' => '1'
            ]);

            $_12->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Classic double room',    
            ]);

            $_12->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Classic Doppelzimmer',    
            ]);

            $_13 = RoomType::create([
                'name' => 'Citadel deluxe double room',
                'status' => '1'
            ]);

            $_13->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Citadel deluxe double room',    
            ]);

            $_13->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Zitadelle Deluxe-Doppelzimmer',    
            ]);

            $_14 = RoomType::create([
                'name' => 'Standard double room',
                'status' => '1'
            ]);

            $_14->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Standard double room',    
            ]);

            $_14->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Standard Doppelzimmer',    
            ]);

            $_15 = RoomType::create([
                'name' => 'Prestige double room',
                'status' => '1'
            ]);

            $_15->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Prestige double room',    
            ]);

            $_15->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Prestige-Doppelzimmer',    
            ]);

            $_16 = RoomType::create([
                'name' => 'Golf Suite 2 Bedroom',
                'status' => '1'
            ]);

            $_16->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Golf Suite 2 Bedroom',    
            ]);

            $_16->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Golf Suite mit 2 Schlafzimmer',    
            ]);

            $_17 = RoomType::create([
                'name' => 'Family',
                'status' => '1'
            ]);

            $_17->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Family',    
            ]);

            $_17->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Familie',    
            ]);

            $_18 = RoomType::create([
                'name' => 'Single room',
                'status' => '1'
            ]);

            $_18->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Single room',    
            ]);

            $_18->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Einzelzimmer',    
            ]);
        }
    }
}
