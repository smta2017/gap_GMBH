<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropFieldsForignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('destination_field_translations', function (Blueprint $table) {
            // if (Schema::hasForeign('destination_field_translations', 'destination_field_translations_field_id_foreign'))
            // {
                // $table->dropForeign('field_id');
            // }
            // $table->foreign('field_id')->references('id')->on('destination_fields')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('destination_field_translations', function (Blueprint $table) {
            //
        });
    }
}
