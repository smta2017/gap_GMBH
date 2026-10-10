<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRebateOffersToHotel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->text('pro_notes')->nullable()->after('iban');
            $table->integer('pro_ratio')->nullable()->after('iban');
            $table->boolean('pro')->nullable()->after('iban');

            $table->text('president_notes')->nullable()->after('iban');
            $table->integer('president_ratio')->nullable()->after('iban');
            $table->boolean('president')->nullable()->after('iban');

            $table->text('travel_agent_notes')->nullable()->after('iban');
            $table->integer('travel_agent_ratio')->nullable()->after('iban');
            $table->boolean('travel_agent')->nullable()->after('iban');

            $table->text('junior_notes')->nullable()->after('iban');
            $table->integer('junior_ratio')->nullable()->after('iban');
            $table->boolean('junior')->nullable()->after('iban');

            $table->text('pro_leader_offer_notes')->nullable()->after('iban');
            $table->integer('pro_leader_offer_number')->nullable()->after('iban');
            $table->boolean('pro_leader_offer')->nullable()->after('iban');


            $table->text('leader_offer_notes')->nullable()->after('iban');
            $table->integer('leader_offer_number')->nullable()->after('iban');
            $table->boolean('leader_offer')->nullable()->after('iban');
          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotels', function (Blueprint $table) {
            //
        });
    }
}
