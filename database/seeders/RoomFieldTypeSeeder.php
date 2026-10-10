<?php

namespace Database\Seeders;

use App\Helper\Helpers;
use Illuminate\Database\Seeder;
use App\Models\RoomFieldType;
use DB;

class RoomFieldTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(RoomFieldType::count() == 0)
        {
            \DB::select('ALTER TABLE room_field_types AUTO_INCREMENT = 1');
            Helpers::create_RoomFieldType('Short Description','Kurzbeschreibung');
            Helpers::create_RoomFieldType('Description','Beschreibungen');
            Helpers::create_RoomFieldType('Number of beds','Bettenzahl');
            Helpers::create_RoomFieldType('size','Zimmergröße');
            Helpers::create_RoomFieldType('Custom Room Type Name','Custom Room Type Name');
        }
    }
}
