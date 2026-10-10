<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomBoard;

class RoomBoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(RoomBoard::count() == 0)
        {
            $_1 = RoomBoard::create([
                'name' => 'Breakfast',
                'status' => '1'
            ]);

            $_1->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Breakfast',    
            ]);

            $_1->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Frühstück',    
            ]);

            $_2 = RoomBoard::create([
                'name' => 'Half board',
                'status' => '1'
            ]);

            $_2->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Half board',    
            ]);

            $_2->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Halbpension',    
            ]);

            $_3 = RoomBoard::create([
                'name' => 'Half board plus',
                'status' => '1'
            ]);

            $_3->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Half board plus',    
            ]);

            $_3->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Halbpension plus',    
            ]);

            $_4 = RoomBoard::create([
                'name' => 'Full board',
                'status' => '1'
            ]);

            $_4->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Full board',    
            ]);

            $_4->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Vollpension',    
            ]);

            $_5 = RoomBoard::create([
                'name' => 'All inclusive',
                'status' => '1'
            ]);

            $_5->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'All inclusive',    
            ]);

            $_5->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Alles Inklusive',    
            ]);

            $_6 = RoomBoard::create([
                'name' => 'Without food',
                'status' => '1'
            ]);

            $_6->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Without food',    
            ]);

            $_6->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Ohne VERPFLEGUNG',    
            ]);

            $_7 = RoomBoard::create([
                'name' => 'Semi',
                'status' => '1'
            ]);

            $_7->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Semi',    
            ]);

            $_7->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Semi',    
            ]);

            $_8 = RoomBoard::create([
                'name' => 'Gourmet Bliss',
                'status' => '1'
            ]);

            $_8->translations()->create([
                'language_id' => '1',
                'locale' => 'en',
                'name' => 'Gourmet Bliss',    
            ]);

            $_8->translations()->create([
                'language_id' => '2',
                'locale' => 'de',
                'name' => 'Gourmet Bliss',    
            ]);
        }
    }
}
