<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomView;

class RoomViewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(RoomView::count() == 0)
        {
            $_1 = RoomView::create([
                'name' => 'Sea view',
                'status' => '1'
            ]);

            $_1->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Sea view',    
            ]);

            $_1->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Meerblick',    
            ]);

            $_2 = RoomView::create([
                'name' => 'Garden view',
                'status' => '1'
            ]);

            $_2->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Garden view',    
            ]);

            $_2->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Gartenblick',    
            ]);

            $_3 = RoomView::create([
                'name' => 'Superior sea view',
                'status' => '1'
            ]);

            $_3->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Superior sea view',    
            ]);

            $_3->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Superior Meerblick',    
            ]);

            $_4 = RoomView::create([
                'name' => 'Pool view',
                'status' => '1'
            ]);

            $_4->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Pool view',    
            ]);

            $_4->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Poolblick',    
            ]);

            $_5 = RoomView::create([
                'name' => 'Golf view',
                'status' => '1'
            ]);

            $_5->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Golf view',    
            ]);

            $_5->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Golfblick',    
            ]);

            $_6 = RoomView::create([
                'name' => 'land side view',
                'status' => '1'
            ]);

            $_6->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'land side view',    
            ]);

            $_6->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Landseite',    
            ]);

            $_7 = RoomView::create([
                'name' => 'Mountain view',
                'status' => '1'
            ]);

            $_7->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Mountain view',    
            ]);

            $_7->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Bergblick',    
            ]);

            $_8 = RoomView::create([
                'name' => 'on the water',
                'status' => '1'
            ]);

            $_8->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'on the water',    
            ]);

            $_8->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'auf dem Wasser',    
            ]);

            $_9 = RoomView::create([
                'name' => 'Beachfront',
                'status' => '1'
            ]);

            $_9->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Beachfront',    
            ]);

            $_9->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Beachfront',    
            ]);
        }
    }
}
