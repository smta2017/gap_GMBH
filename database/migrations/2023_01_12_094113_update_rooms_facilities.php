<?php

use App\Helper\Helpers;
use App\Models\BasicTranslation;
use App\Models\Facility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRoomsFacilities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Facility::where('type', 'Room')->update(['status'=>0]);
       
        Helpers::create_facility('Air condition','Klimaanlage','Room' ,'' ,'', 1);
        Helpers::create_facility('Heating','Heizung','Room' ,'' ,'', 1);
        Helpers::create_facility('Mini bar','Minibar','Room' ,'' ,'', 1);
        Helpers::create_facility('Fridge','Kühlschrank','Room' ,'' ,'', 1);
        Helpers::create_facility('Kettle','Wasserkocher','Room' ,'' ,'', 1);
        Helpers::create_facility('Coffee maker','Kaffeemaschine','Room' ,'' ,'', 1);
        Helpers::create_facility('Nespresso','Nespressomaschine','Room' ,'' ,'', 1);
        Helpers::create_facility('Cooking area','Kitchenette','Room' ,'' ,'', 1);
        Helpers::create_facility('Safe','Safe','Room' ,'' ,'', 1);
        Helpers::create_facility('Smart TV','Smart TV','Room' ,'' ,'', 1);
        Helpers::create_facility('Daily cleaning','Tägliche Reinigung','Room' ,'' ,'', 1);
        Helpers::create_facility('Separate living room','Separater Wohnbereich','Room' ,'' ,'', 1);
        Helpers::create_facility('Desk','Schreibtisch','Room' ,'' ,'', 1);
        Helpers::create_facility('Balcony / Terrace ','Balkon/Terrasse','Room' ,'' ,'', 1);
        Helpers::create_facility('Free Wi-Fi in room','Kostenloses WLAN im Zimmer','Room' ,'' ,'', 1);
        Helpers::create_facility('Lateral Sea View','Seitlicher Meerblick','Room' ,'' ,'', 1);
        Helpers::create_facility('Sea View','Meerblick','Room' ,'' ,'', 1);
        Helpers::create_facility('Garden View','Gartenblick','Room' ,'' ,'', 1);
        Helpers::create_facility('City View','Stadtblick','Room' ,'' ,'', 1);
        Helpers::create_facility('Pool View','Poolblick','Room' ,'' ,'', 1);
        Helpers::create_facility('Golf course View','Blick auf den Golfplatz','Room' ,'' ,'', 1);
        Helpers::create_facility('iPod Dock','iPod-Dockingstation','Room' ,'' ,'', 1);
        Helpers::create_facility('Room Service','Zimmerservice','Room' ,'' ,'', 1);
        Helpers::create_facility('Pillow menu','Kissenmenü','Room' ,'' ,'', 1);
        Helpers::create_facility('Satelite Channel','Satellitenempfang','Room' ,'' ,'', 1);
        Helpers::create_facility('Turndown Service','Turndown Service','Room' ,'' ,'', 1);
        Helpers::create_facility('Disability Room','barrierefreies Zimmer','Room' ,'' ,'', 1);
        Helpers::create_facility('Privater Whirlpool','Privater Whirlpool','Room' ,'' ,'', 1);
        Helpers::create_facility('Private Swimming Pool ','Privater Pool','Room' ,'' ,'', 1);
        Helpers::create_facility('Luxury Amenities','Luxus Amenities','Room' ,'' ,'', 1);
        Helpers::create_facility('Bathroom with bathtub','Bad mit Badewanne','Room' ,'' ,'', 1);
        Helpers::create_facility('Bathroom with shower','Bad mit Dusche','Room' ,'' ,'', 1);
        Helpers::create_facility('Bathroom with shower and bathtub','Bad mit Dusche und Badewanne','Room' ,'' ,'', 1);
        Helpers::create_facility('Bathroom with shower or bathtub','Bad mit Dusche oder Badewanne','Room' ,'' ,'', 1);
        Helpers::create_facility('Rainshower','Regendusche','Room' ,'' ,'', 1);
        Helpers::create_facility('1 bedroom','2 Schlafzimmer','Room' ,'' ,'', 1);
        Helpers::create_facility('2 bedrooms','3 Schlafzimmer','Room' ,'' ,'', 1);
        Helpers::create_facility('3 bedrooms','4 Schlafzimmer','Room' ,'' ,'', 1);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
