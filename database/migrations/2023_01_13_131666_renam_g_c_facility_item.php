<?php

use App\Models\BasicTranslation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\FieldType;

class RenamGCFacilityItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $translate = BasicTranslation::find(2881);
        ($translate) ? $translate->update(['name'=>'General info']) :'';

        $translate = BasicTranslation::find(2882);
        ($translate) ? $translate->update(['name'=>'Hinweise']) :'';

        // $translate = BasicTranslation::find(2873);
        // ($translate) ?? $translate->update(['name'=>'Title']);
        
        // $translate = BasicTranslation::find(2171);
        // ($translate) ?? $translate->update(['name'=>'Title']);

        $fieldtype =  FieldType::find(1);
        ($fieldtype) ? $fieldtype->update(['name'=>'Title']):'';

        $fieldtype =  FieldType::find(4);
        ($fieldtype) ? $fieldtype->update(['name'=>'Why']):'';

        $fieldtype =  FieldType::find(5);
        ($fieldtype) ? $fieldtype->update(['name'=>'What']):'';

        $fieldtype =  FieldType::find(6);
        ($fieldtype) ? $fieldtype->update(['name'=>'Highlights']):'';

        $fieldtype =  FieldType::find(8);
        ($fieldtype) ? $fieldtype->update(['status'=>0]):'';

        $fieldtype =  FieldType::find(10);
        ($fieldtype) ? $fieldtype->update(['status'=>0]):'';

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
