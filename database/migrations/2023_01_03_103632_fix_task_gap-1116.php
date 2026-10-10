<?php

use App\Models\BasicTranslation;
use App\Models\Facility;
use App\Models\Page;
use App\Models\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixTaskGap1116 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Facility::where('type', 'Golf Course')->forceDelete();
        Service::whereIn('name',['Locker','Complementary Water','Launch Package'])->whereType('Golf Course')->update(['active'=>0]);
        Facility::where('type', 'Golf Course')->forceDelete();

        $translate = BasicTranslation::find(2902);
        ($translate) ? $translate->update(['name'=>'Flutlichtgolf']) : '';

        $translate = BasicTranslation::find(124);
        ($translate) ? $translate->update(['name'=>'Golfsclaägerverleih']) : '';


        Page::destroy(26); 

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
